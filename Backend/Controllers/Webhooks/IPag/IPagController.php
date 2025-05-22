<?php

namespace Backend\Controllers\Webhooks\IPag;

use Backend\App;
use Backend\Http\Request;
use Backend\Entities\Abstracts\PaymentWebhook;
use Ezeksoft\PHPWriteLog\Log;
use Backend\Enums\OrderMeta\EOrderMetaPaymentMethod;
use Backend\Enums\IPag\EIPagPaymentStatus;
use Backend\Services\IPag\IPag;

class IPagController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }
        
    public function body() : object
    {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw); 
        $now = date('Y-m-d H:i:s');

        $log = new Log;
        $log->write(base_path('logs/ipag_wook_request.log'), "$now\n$raw" . "\n\n");

        $ipag = new IPag;
        
        // NOTE:
        // * trabalhar com o charge_id nele tem os eventos reembolsado, chargeback, aprovado...
        // * data.id geralmente eh o id da assinatura
        // * charge.id eh o id do pagamento unico
        
        // NOTE:
        // objeto baseado nos arquivos de ./Samples/

        $transaction_id = $json->attributes->order_id ?? '';
        $status = $json->attributes->status->code ?? '';                    // status da cobranca ou assinatura, veja: Backend\Enums\PagarMe\EPagarMeChargeStatus
        $type = $status;                                                    // tipo de cobranca, veja: Backend\Enums\PagarMe\EPagarMeChargeType
        $payment_method = $json->attributes->method ?? '';                  // metodo de pagamento: credit_card, pix, boleto
        $subscription_id = '';                                              // id da assinatura
        
        // se o transaction_id for um id de assinatura
        if (cmp_both_valid($consult_subscription=json_decode($ipag->consultSubscription($transaction_id))->id ?? '', '==', $transaction_id)) 
            $subscription_id = "ipag_$transaction_id";
        $is_recurring_by_id = !empty($subscription_id);                     // pagamento unico ou assinatura
        
        // log dos dados recebidos
        (new Log)->write(base_path('logs/pagarme_wook_request.log'), "[$now]\n$raw\n\n");

        return (object) compact('transaction_id', 'status', 'type', 'payment_method', 'subscription_id', 'is_recurring_by_id');
    }

    public function wook(Request $request)
    {
        extract($body=(array) $this->body());

        // EIPagPaymentStatus::CANCELED
        // EIPagPaymentStatus::PIX_GENERATED
        // EIPagPaymentStatus::BILLET_PRINTED
        // EIPagPaymentStatus::IN_ANALYSIS

        $result = PaymentWebhook::update($body, [
            'gateway' => 'ipag',
            'status' => (object) [
                'PENDING' => EIPagPaymentStatus::CREATED,
                'APPROVED' => EIPagPaymentStatus::CAPTURED,
                'PAYMENT_FAILED' => EIPagPaymentStatus::DENIED,
                'REFUNDED' => EIPagPaymentStatus::REFUNDED,
                'CHARGEDBACK' => EIPagPaymentStatus::CHARGEDBACK,
            ],
            'payment_method' => (object) [
                'CREDIT_CARD' => EOrderMetaPaymentMethod::CREDIT_CARD->value,
                'PIX' => EOrderMetaPaymentMethod::PIX->value,
                'BILLET' => EOrderMetaPaymentMethod::BILLET->value,
            ]
        ]);

        print_r($result);

        // if (empty($transaction_id)) die("[$now] Transação não localizada no iPag.");
        
        // $order = Order::where('transaction_id', $transaction_id)->first();
        // if (empty($order)) die("[$now] O pedido não foi localizado.");

        // $meta_products = OrderMeta::where('order_id', $order->id)->where('name', 'product_id')->get();
        // $meta_orderbumps = OrderMeta::where('order_id', $order->id)->where('name', 'orderbump_items')->first();

        // $customer = Customer::find($order->customer_id);
                
        // $product_names = [];
        // $products = [];
        // foreach ($meta_products as $meta_product)
        // {
        //     $product = Product::find($meta_product->value);
        //     if (!empty($product)) 
        //     {
        //         $product_names[] = $product->name;
        //         $products[] = $product;
        //     }
        // }

        // $meta_orderbumps = json_decode($meta_orderbumps->value);
        // foreach ($meta_orderbumps as $orderbump)
        // {
        //     $product = Product::find($orderbump->product_id);
        //     if (!empty($product)) 
        //     {
        //         $product_names[] = $product->name;
        //         $products[] = $product;
        //     }
        // }

        // $payment_method = $order->meta('info_payment_method');

        // $checkout = Checkout::find($order->checkout_id);
    
        // switch($status)
        // {
        //     // case EIPagPaymentStatus::PRE_AUTHORIZED:
        //     case EIPagPaymentStatus::CAPTURED:
        //         $order->status = EOrderStatus::APPROVED;
        //         $order->status_details = EOrderStatusDetail::APPROVED;
        //         $order->save();

        //         $balance = Balance::where('user_id', $order->user_id)->first();
        //         if (empty($balance))
        //         {
        //             $balance = new Balance;
        //             $balance->user_id = $order->user_id;
        //         }
        //         $balance->amount += (Double) $order->total_seller;

        //         // se for no pix ou boleto, fica disponivel na hora
        //         if ($payment_method == 'pix' || $payment_method == 'billet')
        //         {
        //             $balance->available += (Double) $order->total_seller;
                    
        //             $order->seller_was_credited = 1; // se o saldo foi pago para o vendedor 
        //             $order->seller_credited_at = $now; // data que o saldo vai ser creditado
        //             $order->queue_seller_credit = 0; // nao adiciona na fila de pagamento
        //             $order->save();
        //         }

        //         // se for no cartao, em 14 diass
        //         else if ($payment_method == 'credit_card')
        //         {
        //             // inserir numa fila, caso passe 14 dias sem ter reembolso ou cancelamento
        //             // o valor sera marcado como pago ao vendedor e incrementado no saldo available
        //             $order->seller_was_credited = 0; // se o saldo foi pago para o vendedor 
        //             $order->seller_credited_at = date('Y-m-d H:i:s', strtotime($now . " + 14 days")); // data que o saldo vai ser creditado
        //             $order->queue_seller_credit = 1; // adiciona na fila de pagamento
        //             $order->save();
                    
        //             // se foi cancelado ou reembolsado antes de 14 dias, decrementar do amount e available
        //             // TODO: OK

        //             // se foi cancelado ou reembolsado, descontar valor do amount e available e retirar o vendedor da fila de pagamento caso esteja
        //             // TODO: OK

        //             $balance->future_releases = doubleval($balance->future_releases) + $order->total_seller;
        //         }

        //         $balance->save();

        //         // adicionar o valor em uma tabela + dias que vai ser liberado
        //         // rodar um cronjob para verificar a data de inicio e o dia
        //         // quando chegar a data, creditar o valor em available
                    
        //         $purchases = [];
        //         foreach ($products as $product)
        //         {
        //             $purchase = new Purchase;
        //             $purchase->customer_id = $order->customer_id;
        //             $purchase->product_id = $product->id;
        //             $purchase->order_id = $order->id;
        //             $purchase->status = EPurchaseStatus::ACTIVE;
        //             $purchase->save();
        //             $purchases[] = $purchase;
        //         }

        //         $bestseller = Bestseller::where('product_id', $product?->id)->first();
        //         if (empty($bestseller))
        //         {
        //             $bestseller = new Bestseller;
        //             $bestseller->product_id = $product?->id;
        //         }
        //         $bestseller->sales = intval($bestseller->sales) + 1;
        //         $bestseller->save();

        //         if (!empty($checkout))
        //         {
        //             $checkout->sales = doubleval($checkout->sales) + 1;
        //             $checkout->save();
        //         }

        //         $email_vars = compact('customer', 'products', 'order', 'purchases');
                
        //         Email::to($customer->email)
        //             ->title('Compra aprovada')
        //             ->subject("Você comprou " . join(", ", $product_names))
        //             ->body(Email::view('approvedPurchaseCustomer', $email_vars))
        //             ->send();
        //         break;
                
        //     case EIPagPaymentStatus::CANCELED:
        //         $order->status = EOrderStatus::CANCELED;
        //         $order->status_details = EOrderStatusDetail::CANCELED;
        //         $order->save();

        //         $balance = Balance::where('user_id', $order->user_id)->first();
        //         if (!empty($balance))
        //         {
        //             $balance->amount -= (Double) $order->total_seller;
        //             // so vai retirar do saldo disponivel caso tenha sido depositado la
        //             if ($order->seller_was_credited)
        //                 $balance->available -= (Double) $order->total_seller;
                    
        //             // se esta na fila para ser creditado futuramente e for compra no cartao de credito 
        //             if ($order->queue_seller_credit && $payment_method == 'credit_card')
        //                 $balance->future_releases = doubleval($balance->future_releases) - $order->total_seller;

        //             $balance->save();
        //         }
                
        //         $order->queue_seller_credit = 0; // remove o vendedor da fila para receber o valor da venda
        //         $order->save();
                
        //         $purchase = Purchase::where('order_id', $order->id)->first();
        //         if (!empty($purchase))
        //         {
        //             $purchase->status = EPurchaseStatus::CANCELED;
        //             $purchase->save();

        //             $email_vars = compact('customer', 'products', 'order', 'purchase');
                    
        //             Email::to($customer->email)
        //                 ->title('Compra cancelada')
        //                 ->subject("Cancelamento de " . join(", ", $product_names))
        //                 ->body(Email::view('canceledPurchaseCustomer', $email_vars))
        //                 ->send();
        //         }

        //         break;
        
        //     case EIPagPaymentStatus::REFUNDED:
        //         $order->status = EOrderStatus::CANCELED;
        //         $order->status_details = EOrderStatusDetail::REFUNDED;
        //         $order->save();

        //         $balance = Balance::where('user_id', $order->user_id)->first();
        //         if (!empty($balance))
        //         {
        //             $balance->amount -= (Double) $order->total_seller;
        //             if ($order->seller_was_credited)
        //                 $balance->available -= (Double) $order->total_seller;

        //             // se esta na fila para ser creditado futuramente e for compra no cartao de credito 
        //             if ($order->queue_seller_credit && $payment_method == 'credit_card')
        //                 $balance->future_releases = doubleval($balance->future_releases) - $order->total_seller;

        //             $balance->save();
        //         }
                
        //         $purchase = Purchase::where('order_id', $order->id)->first();
        //         if (!empty($purchase))
        //         {
        //             $purchase->status = EPurchaseStatus::CANCELED;
        //             $purchase->save();
        //         }
                
        //         $order->queue_seller_credit = 0; // remove o vendedor da fila para receber o valor da venda
        //         $order->save();

        //         $email_vars = compact('customer', 'products', 'order', 'purchase');
                
        //         Email::to($customer->email)
        //             ->title('Compra reembolsada')
        //             ->subject("Reembolso de " . join(", ", $product_names))
        //             ->body(Email::view('refundedPurchaseCustomer', $email_vars))
        //             ->send();
        //         break;
    
        //     case EIPagPaymentStatus::CHARGEDBACK:
        //         $order->status = EOrderStatus::CANCELED;
        //         $order->status_details = EOrderStatusDetail::CHARGEDBACK;
        //         $order->save();

        //         $balance = Balance::where('user_id', $order->user_id)->first();
        //         if (!empty($balance))
        //         {
        //             $balance->amount -= (Double) $order->total_seller;
        //             if ($order->seller_was_credited)
        //                 $balance->available -= (Double) $order->total_seller;

        //             // se esta na fila para ser creditado futuramente e for compra no cartao de credito 
        //             if ($order->queue_seller_credit && $payment_method == 'credit_card')
        //                 $balance->future_releases = doubleval($balance->future_releases) - $order->total_seller;

        //             $balance->save();
        //         }
                
        //         $purchase = Purchase::where('order_id', $order->id)->first();
        //         if (!empty($purchase))
        //         {
        //             $purchase->status = EPurchaseStatus::CANCELED;
        //             $purchase->save();
        //         }
                
        //         $order->queue_seller_credit = 0; // remove o vendedor da fila para receber o valor da venda
        //         $order->save();

        //         $email_vars = compact('customer', 'products', 'order', 'purchase');
                
        //         Email::to($customer->email)
        //             ->title('Compra contestada')
        //             ->subject("Chargeback de " . join(", ", $product_names))
        //             ->body(Email::view('chargedbackPurchaseCustomer', $email_vars))
        //             ->send();
        //         break;

        //     case EIPagPaymentStatus::PIX_GENERATED:
        //         $order->status = EOrderStatus::PENDING;
        //         $order->status_details = EOrderStatusDetail::PIX_GENERATED;
        //         $order->save();

        //         $email_vars = compact('customer', 'products', 'order');
                
        //         Email::to($customer->email)
        //             ->title('Pix gerado')
        //             ->subject("Compra pendente de " . join(", ", $product_names))
        //             ->body(Email::view('pixGeneratedPurchaseCustomer', $email_vars))
        //             ->send();
        //         break;

        //     case EIPagPaymentStatus::BILLET_PRINTED:
        //         $order->status = EOrderStatus::PENDING;
        //         $order->status_details = EOrderStatusDetail::BILLET_PRINTED;
        //         $order->save();

        //         $email_vars = compact('customer', 'products', 'order');
                
        //         Email::to($customer->email)
        //             ->title('Boleto gerado')
        //             ->subject("Compra pendente de " . join(", ", $product_names))
        //             ->body(Email::view('billetPrintedPurchaseCustomer', $email_vars))
        //             ->send();
        //         break;

        //     case EIPagPaymentStatus::IN_ANALYSIS:
        //         $email_vars = compact('customer', 'products', 'order');
                
        //         Email::to($customer->email)
        //             ->title('Em análise')
        //             ->subject("Compra pendente de " . join(", ", $product_names))
        //             ->body(Email::view('inAnalysisPurchaseCustomer', $email_vars))
        //             ->send();
        //         break;

        //     case EIPagPaymentStatus::DENIED:
        //         $order->status = EOrderStatus::CANCELED;
        //         $order->status_details = EOrderStatusDetail::REJECTED;
        //         $order->save();

        //         $balance = Balance::where('user_id', $order->user_id)->first();
        //         if (!empty($balance))
        //         {
        //             $balance->amount -= (Double) $order->total_seller;
        //             if ($order->seller_was_credited)
        //                 $balance->available -= (Double) $order->total_seller;
                        
        //             // se esta na fila para ser creditado futuramente e for compra no cartao de credito 
        //             if ($order->queue_seller_credit && $payment_method == 'credit_card')
        //                 $balance->future_releases = doubleval($balance->future_releases) - $order->total_seller;

        //             $balance->save();
        //         }
                
        //         $order->queue_seller_credit = 0; // remove o vendedor da fila para receber o valor da venda
        //         $order->save();
                
        //         $purchase = Purchase::where('order_id', $order->id)->first();
        //         if (!empty($purchase))
        //         {
        //             $purchase->status = EPurchaseStatus::CANCELED;
        //             $purchase->save();

        //             $email_vars = compact('customer', 'products', 'order', 'purchase');
                    
        //             Email::to($customer->email)
        //                 ->title('Compra negada')
        //                 ->subject("Tentativa de pagar " . join(", ", $product_names))
        //                 ->body(Email::view('deniedPurchaseCustomer', $email_vars))
        //                 ->send();
        //         }
        //         break;
        // }
    }
}