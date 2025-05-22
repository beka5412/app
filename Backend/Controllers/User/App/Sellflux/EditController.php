<?php

namespace Backend\Controllers\User\App\Sellflux;

use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Exceptions\App\Astronmembers\EmptyClassidException;
use Backend\Exceptions\App\Astronmembers\EmptyPasswordException;
use Backend\Exceptions\App\Astronmembers\EmptyUsernameException;
use Backend\Exceptions\App\Memberkit\EmptyProductException;
use Backend\Exceptions\App\Sellflux\AppSellfluxIntegrationNotFound;
use Backend\Exceptions\App\Sellflux\EmptyLinkException;
use Backend\Models\AppSellfluxIntegration;
use Backend\Models\Product;

class EditController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Sellflux integration';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/apps/sellflux/editView.php';

    public function view(string $view_method, Request $request, array $params=[])
    {
        extract((array) $this);
        extract($params);

        $view = new View;

        try
        {
            $integration = AppSellfluxIntegration::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($integration)) throw new ModelNotFoundException;

            $products = Product::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

            $view = View::$view_method($this->indexFile, compact('title', 'context', 'user', 'integration', 'products'));
        }

        catch (ModelNotFoundException)
        {
            $notfound = new NotFoundController($this->application);
            $view = $notfound->element(new Request);
        }

        return $view;
    }

    public function update(Request $request, $id)
    {
        $user = $this->user;
        $body = $request->json();

        $enabled = $body->enabled ?? null;
        $link = $body->link ?? null;
        $product_id = $body->product_id ?? null;

        try
        {
            $integration = AppSellfluxIntegration::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($integration)) throw new AppSellfluxIntegrationNotFound;

            if (!$link) throw new EmptyLinkException;
            if (!$product_id) throw new EmptyProductException;

            $integration->status = $enabled;
            $integration->link = aes_encode_db($link);
            $integration->product_id = $product_id;
            $integration->save();

            $response_data = new ResponseData(['status' => 'success', 'message' => __('Sellflux API key updated successfully.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (AppSellfluxIntegrationNotFound)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('Sellflux integration not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyLinkException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The api key is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyProductException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The product is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (Exception $ex)
        {
            if (in_debug())
                $response_data = new ResponseData(['status' => 'error', 'message' => $ex->getMessage()]);
            else
                $response_data = new ResponseData(['status' => 'error', 'message' => __('Internal error.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
}
