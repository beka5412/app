<?php

namespace Backend\Controllers\Webhooks\Iugu;

use Backend\Controllers\Controller\TController;
use Backend\Entities\Abstracts\Iugu\IuguWebhookEvent;
use Backend\Entities\Abstracts\SellerBalance;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Enums\Purchase\EPurchaseStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\Checkout;
use Backend\Models\Customer;
use Backend\Models\IuguSeller;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Models\Purchase;
use Backend\Models\User;
use Backend\Types\Iugu\Events\Data\EIuguDataStatus;
use Backend\Types\Iugu\Events\EIuguInvoice;
use Backend\Types\Iugu\Events\EIuguSubscription;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Ezeksoft\PHPWriteLog\Log;
use Setono\MetaConversionsApi\Event\Custom;

class IuguController
{
    use TController;

    public function wook(Request $request)
    {
        $event = $request->query('event');
        $data = $request->query('data');
        $id = $data['id'];
        $status = $data['status'];
        $status = $data['status'];
        $account_id = $data['account_id'];

        (new Log)->write(base_path('logs/iugu_webhook_data.log'), "\n\n[".date('Y-m-d H:i:s')."]\n".json_encode($request->all()));

        $iugu_seller = IuguSeller::where('account_id', $account_id)->first();
        $iugu_seller_account_id = $iugu_seller->account_id ?? '';

        if (!$account_id || ($account_id <> env('IUGU_ACCOUNT_ID') && $account_id <> $iugu_seller_account_id)) return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Invalid iugu account.'
            ]),
            new ResponseStatus('400 Bad Request')
        );

        if ($event === EIuguInvoice::STATUS_CHANGED->value && $status === EIuguDataStatus::PAID->value) 
            return IuguWebhookEvent::invoice()->paid($request);

        else if ($event === EIuguInvoice::STATUS_CHANGED->value && $status === EIuguDataStatus::REFUNDED->value) 
            return IuguWebhookEvent::invoice()->refunded($request);

        else if ($event === EIuguInvoice::STATUS_CHANGED->value && $status === EIuguDataStatus::CHARGEBACK->value) 
            return IuguWebhookEvent::invoice()->chargeback($request);
        
        else if ($event === EIuguSubscription::RENEWED->value) 
            return IuguWebhookEvent::subscription()->renewed($request);

        return Response::json(
            new ResponseData([
                'status' => EResponseDataStatus::SUCCESS,
                'message' => 'No event captured.'
            ]),
            new ResponseStatus('200 OK')
        );
    }
}
