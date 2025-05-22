<?php 

namespace Backend\Controllers\Webhooks\PagarMe;

use Backend\App;
use Backend\Http\Request;
use Ezeksoft\PHPWriteLog\Log;
use Backend\Entities\Abstracts\PaymentWebhook;
use Backend\Enums\PagarMe\EPagarMeChargeType;
use Backend\Enums\PagarMe\EPagarMeChargeStatus;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\PagarMe\EPagarMePaymentMethod;

class PagarMeController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }
    
    public function body() : object
    {
        $json = file_get_contents('php://input');
        $object = json_decode($json); 
        $now = date('Y-m-d H:i:s');
        
        // NOTE:
        // * trabalhar com o charge_id nele tem os eventos reembolsado, chargeback, aprovado...
        // * data.id geralmente eh o id da assinatura
        // * charge.id eh o id do pagamento unico
        
        // NOTE:
        // objeto baseado nos arquivos de ./Samples/

        $charges = $object->data->charges ?? [];                            // lista de cobrancas
        $charge = $charges[0] ?? null;                                      // primeira cobranca da lista
        $transaction_id = $charge->id ?? $object->data->id ?? '';           // id da cobranca
        $status = $charge->status ?? $object->data->status ?? '';           // status da cobranca ou assinatura, veja: Backend\Enums\PagarMe\EPagarMeChargeStatus
        $type = $object?->type ?? '';                                       // tipo de cobranca, veja: Backend\Enums\PagarMe\EPagarMeChargeType
        $payment_method = $charge?->payment_method ?? '';                   // metodo de pagamento: credit_card, pix, boleto
        $subscription_id = $object?->data?->invoice?->subscriptionId ?? ''; // id da assinatura
        $is_recurring_by_id = !empty($subscription_id);                     // pagamento unico ou assinatura
        
        // log dos dados recebidos
        (new Log)->write(base_path('logs/pagarme_wook_request.log'), "[$now]\n$json\n\n");

        return (object) compact('charge', 'transaction_id', 'status', 'type', 'payment_method', 'subscription_id', 'is_recurring_by_id');
    }

    public function wook(Request $request)
    {
        $json = file_get_contents('php://input');
        $object = json_decode($json); 
        $now = date('Y-m-d H:i:s');
        extract($body=(array) $this->body());
        $account_id = $object?->account?->id ?? ''; // id da conta vendor

        // garante que a conta utilizada no pagar.me eh a que esta no .env
        if ($account_id <> env('PAGARME_ACCOUNT_ID')) die("[$now] Webhook não autenticado.");
        if (empty($transaction_id)) die("[$now] Transação não localizada no pagar.me.");
        
        $result = PaymentWebhook::update($body, [
            'gateway' => 'pagarme',
            'status' => (object) [
                'PENDING' => EPagarMeChargeStatus::PENDING,
                'APPROVED' => EPagarMeChargeType::PAID,
                'PAYMENT_FAILED' => EPagarMeChargeType::PAYMENT_FAILED,
                'REFUNDED' => EPagarMeChargeType::REFUNDED,
                'CHARGEDBACK' => EPagarMeChargeType::CHARGEDBACK,
            ],
            'payment_method' => (object) [
                'CREDIT_CARD' => EOrderMetaPaymentMethod::CREDIT_CARD->value,
                'PIX' => EOrderMetaPaymentMethod::PIX->value,
                'BILLET' => EPagarMePaymentMethod::BILLET,
            ]
        ]);
    }
}
