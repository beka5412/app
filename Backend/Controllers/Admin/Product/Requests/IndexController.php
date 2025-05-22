<?php

namespace Backend\Controllers\Admin\Product\Requests;

use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Enums\Product\EProductRequestStatus;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\Product;
use Backend\Models\ProductRequest;
use Backend\Models\User;
use Backend\Template\View;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Astron Members integrations';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/admin/products/requests/indexView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/admin/product/requests';
        $view = new View;

        try
        {
            $product_requests = ProductRequest::with('product')->where('status', 'pending')->orderBy('id', 'DESC')
                ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page)->onEachSide(2);

            $info = $product_requests;
            
            $view = View::$view_method($this->indexFile, compact('title', 'context', 'user', 'admin', 'product_requests', 'info', 'url'));
        }

        catch (ModelNotFoundException)
        {
            $notfound = new NotFoundController($this->application);
            $view = $notfound->element(new Request);
        }

        return $view;
    }

    public function approve(Request $request, $id)
    {
        try 
        {
            $product_request = ProductRequest::findOrFail($id);
            $product_request->status = EProductRequestStatus::APPROVED;
            $product_request->answered_at = today();
            $product_request->save();

            $product = Product::findOrFail($product_request->product_id);
            $product->approved = 1;
            $product->save();

            $user = User::find($product_request->user_id);
        
            $email_data = [
                'site_url' => site_url(),
                'platform' => site_name(),
                'username' => $user->name,
                'product_name' => $product->name,
                'product_image' => site_url().$product->image,
                'total' => number_to_currency_by_symbol($product->price_promo ?: $product->price, 'brl'),
                'symbol' => currency_code_to_symbol('brl')->value,
            ];
            
            send_email($user->email, $email_data, EEmailTemplatePath::APPROVED_PRODUCT, 'pt_BR');

            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Product approved.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (ModelNotFoundException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Product request not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        send_email($user->email, $email_data, EEmailTemplatePath::APPROVED_PRODUCT, 'pt_BR');

        return Response::json($response_data, $response_status);
    }
    public function reject(Request $request, $id)
    {
        try 
        {
            $product_request = ProductRequest::findOrFail($id);
            $product_request->status = EProductRequestStatus::REJECTED;
            $product_request->answered_at = today();
            $product_request->save();

            $product = Product::findOrFail($product_request->product_id);
            $product->approved = 0;
            $product->save();

            $user = User::find($product_request->user_id);
        
            $email_data = [
                'site_url' => site_url(),
                'platform' => site_name(),
                'username' => $user->name,
                'product_name' => $product->name,
                'product_image' => site_url().$product->image,
                'total' => number_to_currency_by_symbol($product->price_promo ?: $product->price, 'brl'),
                'symbol' => currency_code_to_symbol('brl')->value,
                'reason' => '' // TODO: motivo da rejeição do produto (incluir tag <p>)
            ];
            
            send_email($user->email, $email_data, EEmailTemplatePath::REJECTED_PRODUCT, 'pt_BR');

            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Product rejected.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (ModelNotFoundException)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::SUCCESS, 'message' => __('Product request not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
}
