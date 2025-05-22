<?php

namespace Backend\Controllers\User\App\Cademi;

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
use Backend\Exceptions\App\Cademi\AppCademiIntegrationNotFound;
use Backend\Exceptions\App\Cademi\EmptyProductException;
use Backend\Exceptions\App\Cademi\EmptySubdomainException;
use Backend\Exceptions\App\Cademi\EmptyTokenException;
use Backend\Models\AppCademiIntegration;
use Backend\Models\Product;

class EditController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Cademi integration';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/apps/cademi/editView.php';

    public function view(string $view_method, Request $request, array $params = [])
    {
        extract((array) $this);
        extract($params);

        $view = new View;

        try
        {
            $integration = AppCademiIntegration::where('id', $id)->where('user_id', $user->id)->first();
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
        $subdomain = $body->subdomain ?? null;
        $token = $body->token ?? null;
        $product_id = $body->product_id ?? null;

        try
        {
            $integration = AppCademiIntegration::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($integration)) throw new AppCademiIntegrationNotFound;

            if (!$subdomain) throw new EmptySubdomainException;
            if (!$token) throw new EmptyTokenException;
            if (!$product_id) throw new EmptyProductException;

            $integration->status = $enabled;
            $integration->subdomain = $subdomain;
            $integration->token = aes_encode_db($token);
            $integration->product_id = $product_id;
            $integration->save();

            $response_data = new ResponseData(['status' => 'success', 'message' => __('Cademi integration updated successfully.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (AppCademiIntegrationNotFound)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('Cademi integration not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyProductException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The product is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptySubdomainException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The subdomain is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyTokenException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The token is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (Exception $ex)
        {
            $response_data = in_debug()
                ? new ResponseData(['status' => 'error', 'message' => $ex->getMessage()])
                : new ResponseData(['status' => 'error', 'message' => __('Internal error.')]);

            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }
}
