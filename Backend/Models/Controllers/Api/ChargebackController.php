<?php

namespace Backend\Controllers\Api;
use Backend\Controllers\Controller\TController;
use Backend\Attributes\Route;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\IuguSeller;
use Backend\Models\Order;
use Backend\Models\PaylabChargebackAlert;
use Backend\Services\Iugu\IuguRest;
use Backend\Services\Paylab\PaylabRest;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Ezeksoft\PHPWriteLog\Log;

class ChargebackController
{
    use TController;

    protected function auth(Request $request): bool
    {
        $client_id = $request->header('Clientid');
        $client_key = $request->header('Clientkey');

        return cmp_both_valid($client_id, '==', env('PAYLAB_CLIENT_ID')) && cmp_both_valid($client_key, '==', env('PAYLAB_CLIENT_KEY'));
    }
    
    public function master(Request $request): Response
    {
        if (!$this->auth($request))
        {
            return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Unauthorized.'
                ]),
                new ResponseStatus('401 Unauthorized')
            );
        }

        // (new Log)->write(base_path('logs/master.log'), "\n[" . today() . "]\n" . $request->raw());

        $body = $request->json();

        $id_date = preg_replace('/\D/', '', $body->TransactionDate);
        $id_card = preg_replace('/\D/', '', $body->CardNumber);
        $id_price = preg_replace('/\D/', '', $body->Amount);

        $alert_id_old = "$id_price-$id_date-$id_card";
        $alert_id = "$id_date-$id_card";

        $data = [
            'alert_id' => $alert_id,
            'api_alert_id' => $body->AlertId,
            'api_transaction_date' => $body->TransactionDate,
            'api_amount' => $body->Amount,
            'api_auth_code' => $body->AuthCode,
            'api_card_number' => $body->CardNumber,
            'api_merchant' => $body->Merchant,
            'api_merchant_descriptor' => $body->MerchantDescriptor,
            'api_received_date' => $body->ReceivedDate,
            'api_issuer' => $body->Issuer,
            'api_transaction_type' => $body->TransactionType,
            'api_source' => $body->Source,
            'api_status' => $body->Status,
            'api_type' => $body->Type
        ];

        $order = Order::where('alert_id', $alert_id)->orWhere('alert_id', $alert_id_old)->first();
        $invoice_id = $order->transaction_id ?? '';
        $data['order_id'] = $order->id ?? null;

        // TODO: encontrar transacao de pagamento de recorrência para caso de chargeback de uma mensalidade de assinatura

        $paylab_chargeback_alert = PaylabChargebackAlert::create($data);

        $iugu_seller = ($order->user_id ?? false) ? IuguSeller::where('user_id', $order->user_id)->orderBy('id', 'DESC')->first() : null;
        $iugu_token = ($iugu_seller->live_api_token ?? '') ?: env('IUGU_API_TOKEN');

        $iugu_response = $invoice_id ? IuguRest::request(
            verb: 'POST',
            url: "/invoices/$invoice_id/refund?api_token=$iugu_token",
            headers: ['Content-Type' => 'application/json'],
            timeout: 10
        ) : null;

        /**
         * ACCOUNT_SUSPENDED | NOTFOUND | OTHER
         */
        $status = ($iugu_response->json->status ?? '') === 'refunded' ? 'ACCOUNT_SUSPENDED' : 'NOTFOUND';

        $paylab_rest_response = PaylabRest::request(
            verb: 'POST',
            url: '/alert/client/master/update',
            headers: [
                'Content-Type' => 'application/json',
                'ClientId' => env('PAYLAB_CLIENT_ID'),
                'ClientKey' => env('PAYLAB_CLIENT_KEY')
            ],
            body: json_encode([
                "AlertId" => $data['api_alert_id'],
                "Status" => $status,
            ]),
            timeout: 10
        );

        $paylab_chargeback_alert->paylab_result_status = $status;
        $paylab_chargeback_alert->save();

        // (new Log)->write(base_path('logs/master_chargeback_postback.log'), "\n[" . today() . "]\n" . $paylab_rest_response->body);

        if ($status === 'refunded')
        {
            $order->status = EOrderStatus::CANCELED;
            $order->status_details = EOrderStatusDetail::CHARGEDBACK;
            $order->save();
        }

        $data = [
            'status' => EResponseDataStatus::SUCCESS,
            'data' => ['alert_id' => $alert_id]
        ];

        return Response::json(new ResponseData($data), new ResponseStatus('200 OK'), true);
    }

    public function visa(Request $request): Response
    {
        if (!$this->auth($request))
        {
            return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::ERROR,
                    'message' => 'Unauthorized.'
                ]),
                new ResponseStatus('401 Unauthorized')
            );
        }

        // (new Log)->write(base_path('logs/visa.log'), "\n[" . today() . "]\n" . $request->raw());

        $body = $request->json();

        $id_date = preg_replace('/\D/', '', $body->TransactionDate);
        $id_card = preg_replace('/\D/', '', $body->CardNumber);
        $id_price = preg_replace('/\D/', '', $body->Amount);

        $alert_id = "$id_price-$id_date-$id_card";

        $data = [
            'alert_id' => $alert_id,
            'api_alert_id' => $body->AlertId,
            'api_transaction_date' => $body->TransactionDate,
            'api_amount' => $body->Amount,
            'api_auth_code' => $body->AuthCode,
            'api_card_number' => $body->CardNumber,
            'api_merchant' => $body->Merchant,
            'api_merchant_descriptor' => $body->MerchantDescriptor,
            'api_received_date' => $body->ReceivedDate,
            'api_issuer' => $body->Issuer,
            'api_transaction_type' => $body->TransactionType,
            'api_source' => $body->Source,
            'api_status' => $body->Status,
            'api_type' => $body->Type
        ];

        $order = Order::where('alert_id', $alert_id)->first();
        $invoice_id = $order->transaction_id ?? '';
        $data['order_id'] = $order->id ?? null;

        $paylab_chargeback_alert = PaylabChargebackAlert::create($data);

        $iugu_seller = ($order->user_id ?? false) ? IuguSeller::where('user_id', $order->user_id)->orderBy('id', 'DESC')->first() : null;
        $iugu_token = ($iugu_seller->live_api_token ?? '') ?: env('IUGU_API_TOKEN');

        $iugu_response = $iugu_token ? IuguRest::request(
            verb: 'POST',
            url: "/invoices/$invoice_id/refund?api_token=$iugu_token",
            headers: ['Content-Type' => 'application/json'],
            timeout: 10
        ) : null;

        /**
         * ACCOUNT_SUSPENDED | NOTFOUND | OTHER
         */
        $status = ($iugu_response->json->status ?? '') === 'refunded' ? 'ACCOUNT_SUSPENDED' : 'NOTFOUND';

        $paylab_rest_response = PaylabRest::request(
            verb: 'POST',
            url: '/alert/client/master/update',
            headers: [
                'Content-Type' => 'application/json',
                'ClientId' => env('PAYLAB_CLIENT_ID'),
                'ClientKey' => env('PAYLAB_CLIENT_KEY')
            ],
            body: json_encode([
                "AlertId" => $data['api_alert_id'],
                "Status" => $status,
            ]),
            timeout: 10
        );

        $paylab_chargeback_alert->paylab_result_status = $status;
        $paylab_chargeback_alert->save();

        // (new Log)->write(base_path('logs/visa_chargeback_postback.log'), "\n[" . today() . "]\n" . $paylab_rest_response->body);

        if ($status === 'refunded')
        {
            $order->status = EOrderStatus::CANCELED;
            $order->status_details = EOrderStatusDetail::CHARGEDBACK;
            $order->save();
        }

        $data = [
            'status' => EResponseDataStatus::SUCCESS,
            'data' => ['alert_id' => $alert_id]
        ];

        return Response::json(new ResponseData($data), new ResponseStatus('200 OK'), true);
    }
}
