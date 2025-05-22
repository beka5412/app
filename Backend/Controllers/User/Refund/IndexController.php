<?php
declare(strict_types=1);

namespace Backend\Controllers\User\Refund;

use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Enums\RefundRequest\ERefundRequestStatus;
use Backend\Exceptions\RefundRequest\EmptyStripePaymentIntentException;
use Backend\Exceptions\RefundRequest\Stripe\EmptyPaymentIntentException;
use Backend\Exceptions\RefundRequest\Stripe\EmptyRefundException;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\RefundRequest;
use Backend\Template\View;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Refunds';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/refunds/indexView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/refunds';
        $view = new View;

        try
        {
            $refund_requests = RefundRequest::where('user_id', $user->id)
                ->with('user')
                ->with('customer')
                ->with(['purchase' => function($query) {
                    $query->with(['order' => function($query) {
                        $query->with('stripe_payment_intent');
                    }]);
                }])
                ->orderBy('id', 'DESC')
                ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page)->onEachSide(2);

            $info = $refund_requests;
            
            $view = View::$view_method($this->indexFile, compact('title', 'context', 'user', 'refund_requests', 'info', 'url'));
        }

        catch (ModelNotFoundException)
        {
            $notfound = new NotFoundController($this->application);
            $view = $notfound->element(new Request);
        }

        return $view;
    }

    public function confirm(Request $request, $id): Response
    {
        extract((array) $this);

        try 
        {
            $refund_request = RefundRequest::where('user_id', $user->id)
                ->where('id', $id)
                ->with(['purchase' => function($query) {
                    $query->with(['order' => function($query) {
                        $query->with('stripe_payment_intent');
                    }]);
                }])
                ->first();
            if (!$refund_request) throw new ModelNotFoundException;

            $payment_intent_id = $refund_request->purchase->order->stripe_payment_intent->payment_intent ?? '';
            
            $stripe = new \Stripe\StripeClient([
                'api_key' => env('STRIPE_SECRET'),
                'stripe_version' => '2023-10-16',
            ]);
          
            if (!$payment_intent_id) throw new EmptyStripePaymentIntentException;
            $payment_intent = $stripe->paymentIntents->retrieve($payment_intent_id);

            if (!$payment_intent) throw new EmptyPaymentIntentException;
            $refunds = $stripe->refunds->create(['charge' => $payment_intent->latest_charge]);

            if (!$refunds) throw new EmptyRefundException;
           
            $refund_request->status = ERefundRequestStatus::CONFIRMED;
            $refund_request->refunded_at = today();
            $refund_request->save();
        
            $response_data = new ResponseData([
                'data' => $payment_intent,
                'status' => EResponseDataStatus::SUCCESS, 'message' => 'Reembolso realizado com sucesso.']);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (EmptyStripePaymentIntentException|EmptyPaymentIntentException|EmptyPaymentIntentException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'Pagamento não localizado.']);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (ModelNotFoundException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'Solicitação de reembolso não encontrada.']);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }

    public function cancel(Request $request, $id): Response
    {
        extract((array) $this);

        try
        {
            $refund_request = RefundRequest::where('user_id', $user->id)->where('id', $id)->first();
            if (!$refund_request) throw new ModelNotFoundException;

            $refund_request->status = ERefundRequestStatus::CANCELED;
            $refund_request->save();

            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => 'Reembolso cancelado com sucesso.']);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (ModelNotFoundException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => 'Solicitação de reembolso não encontrada.']);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
}
