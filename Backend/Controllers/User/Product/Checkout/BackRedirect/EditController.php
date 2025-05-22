<?php

namespace Backend\Controllers\User\Product\Checkout\BackRedirect;

use Backend\App;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Exceptions\Checkout\BackRedirect\EmptyBackRedirectUrlException;
use Backend\Exceptions\Checkout\BackRedirect\ProductNotFoundException;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\Checkout;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Exception;

class EditController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Back Redirect';
    public string $context = 'dashboard';
    public string $indexFile = '';

    public function update(Request $request, mixed $product_id, mixed $checkout_id): Response
    {
        $user = $this->user;
        $body = $request->json();
        $backredirect_enabled = $body->backredirect_enabled ?? '';
        $backredirect_url = $body->backredirect_url ?? '';

        try
        {
            $checkout = Checkout::where('id', $checkout_id)->where('user_id', $user->id)->first();

            if (empty($checkout)) throw new ProductNotFoundException;
            if ($backredirect_enabled && !$backredirect_url) throw new EmptyBackRedirectUrlException;

            $checkout->backredirect_enabled = $backredirect_enabled;
            $checkout->backredirect_url = $backredirect_url;
            $checkout->save();

            $response_data =
                new ResponseData([
                    'status' => EResponseDataStatus::SUCCESS,
                    'message' => __('Checkout updated successfully.'),
                    'data' => $checkout
                ]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (ProductNotFoundException $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Product not found.')]);
            $response_status = new ResponseStatus('404 Not Found');
        }

        catch (EmptyBackRedirectUrlException $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('The url cannot be blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (Exception $ex)
        {
            $response_data = new ResponseData(['status' => EResponseDataStatus::ERROR, 'message' => __('Internal error.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
}
