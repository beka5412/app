<?php

namespace Backend\Controllers\Subdomains\Purchase\Dashboard;

use Backend\App;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Enums\RefundRequest\ERefundRequestStatus;
use Backend\Exceptions\Purchase\PurchaseNotFoundException;
use Backend\Exceptions\RefundRequest\ThereIsAlreadyRefundRequestException;
use Backend\Exceptions\RefundRequest\RefundRequestNotFoundException;
use Backend\Exceptions\RefundRequest\CanceledAlreadyRefundRequestException;
use Backend\Exceptions\RefundRequest\ConfirmedAlreadyRefundRequestException;
use Backend\Models\Customer;
use Backend\Models\Purchase;
use Backend\Models\RefundRequest;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Purchase';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/subdomains/purchase/dashboard/indexView.php';
        $this->customer = customer();
        $this->subdomain = 'purchase';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $customer = $this->customer;

        $purchases = Purchase::where('customer_id', $customer->id)
            ->with(['customer' => function($query) {
                $query->with('user');
            }])
            ->with(['product' => function($query) {
                $query->with('user');
            }])
            ->with('refund_request')
            ->with('order')
        ->orderBy('id', 'DESC')->paginate(10);

        $metas = [
            'payment_pix_code',
            'payment_pix_image',
            'product_id',
            'address_street',
            'address_number',
            'address_district',
            'address_complement',
            'address_city',
            'address_state',
            'address_zipcode',
            'customer_name',
            'customer_email',
            'customer_cpf_cnpj',
            'customer_phone',
            'info_total',
            'info_payment_method',
            'info_flag'
        ];

        foreach ($purchases as $purchase)
        {
            if ($purchase->order ?? '')
            {
                $arr = [];
                foreach ($metas as $meta)
                    $arr[$meta] = get_ordermeta($purchase->order->id, $meta);
                
                $purchase->order->meta = json_decode(json_encode($arr));
            }
        }

        $locale = $purchases[0]->order->lang ?? 'pt_BR';
        setcookie('locale', $locale);

        View::render($this->indexFile, compact('title', 'context', 'customer', 'purchases', 'locale'));
    }

    public function element(Request $request)
    {

        $title = $this->title;
        $context = $this->context;
        $customer = $this->customer;

        $purchases = Purchase::where('customer_id', $customer->id)
            ->with(['customer' => function($query) {
                $query->with('user');
            }])
            ->with(['product' => function($query) {
                $query->with('user');
            }])
            ->with('refund_request')
            ->with('order')
        ->orderBy('id', 'DESC')->paginate(10);

        $metas = [
            'payment_pix_code',
            'payment_pix_image',
            'product_id',
            'address_street',
            'address_number',
            'address_district',
            'address_complement',
            'address_city',
            'address_state',
            'address_zipcode',
            'customer_name',
            'customer_email',
            'customer_cpf_cnpj',
            'customer_phone',
            'info_total',
            'info_payment_method',
            'info_flag'
        ];

        foreach ($purchases as $purchase)
        {
            if ($purchase->order ?? '')
            {
                $arr = [];
                foreach ($metas as $meta)
                    $arr[$meta] = get_ordermeta($purchase->order->id, $meta);
                
                $purchase->order->meta = json_decode(json_encode($arr));
            }
        }

        $locale = $purchases[0]->order->lang ?? 'pt_BR';
        setcookie('locale', $locale);

        View::response($this->indexFile, compact('title', 'context', 'customer', 'purchases', 'locale'));
    }

    public function refund(Request $request, $purchase_id)
    {
        $title = $this->title;
        $context = $this->context;
        $customer = $this->customer;

        $body = $request->json();
        $reason = $body?->reason ?? '';

        $response = [];

        try 
        {
            $purchase = Purchase::with('product')->find($purchase_id);
            if (empty($purchase)) return;


            $refund_request = RefundRequest::where('purchase_id', $purchase_id)->where('customer_id', $customer->id)->first();
            if (empty($refund_request))
            {
                $refund_request = new RefundRequest;
                $refund_request->purchase_id = $purchase_id;
                $refund_request->customer_id = $customer->id;
                $refund_request->user_id = $purchase->product->user_id;
            }

            else
            {
                if ($refund_request->status == ERefundRequestStatus::PENDING->value)
                    throw new ThereIsAlreadyRefundRequestException;
                if ($refund_request->status == ERefundRequestStatus::CONFIRMED->value)
                    throw new ConfirmedAlreadyRefundRequestException;
                    
                $refund_request->status = ERefundRequestStatus::PENDING->value;
            }

            $refund_request->reason = $reason;
            $refund_request->save();

            $response = ["status" => "success", "message" => "Solicitação realizada com sucesso."];
        }

        catch (ThereIsAlreadyRefundRequestException $ex)
        {
            $response = ["status" => "error", "message" => "Já existe uma solicitação de reembolso para esta compra."];
        }

        catch (ConfirmedAlreadyRefundRequestException $ex)
        {
            $response = ["status" => "error", "message" => "Erro! Este reembolso já foi realizado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function cancelRefund(Request $request, $purchase_id)
    {
        $title = $this->title;
        $context = $this->context;
        $customer = $this->customer;

        $response = [];

        try 
        {
            $refund_request = RefundRequest::where('purchase_id', $purchase_id)->where('customer_id', $customer->id)->first();
            if (empty($refund_request))
                throw new RefundRequestNotFoundException;
            
            if ($refund_request->status == ERefundRequestStatus::CANCELED->value) throw new CanceledAlreadyRefundRequestException;
            if ($refund_request->status == ERefundRequestStatus::CONFIRMED->value) throw new ConfirmedAlreadyRefundRequestException;

            $refund_request->status = ERefundRequestStatus::CANCELED->value;
            $refund_request->save();

            $response = ["status" => "success", "message" => "Solicitação cancelada com sucesso."];
        }

        catch (RefundRequestNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Esta solicitação não foi encontrada."];
        }

        catch (CanceledAlreadyRefundRequestException $ex)
        {
            $response = ["status" => "error", "message" => "Este cancelamento já foi efetuado anteriormente."];
        }

        catch (ConfirmedAlreadyRefundRequestException $ex)
        {
            $response = ["status" => "error", "message" => "Erro! Este reembolso já foi realizado."];
        }

        finally
        {
            Response::json($response);
        }
    }
}