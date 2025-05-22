<?php

namespace Backend\Controllers\User\App\Astronmembers;

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
use Backend\Exceptions\App\Astronmembers\AppAstronmembersIntegrationNotFound;
use Backend\Exceptions\App\Astronmembers\EmptyClassidException;
use Backend\Exceptions\App\Astronmembers\EmptyPasswordException;
use Backend\Exceptions\App\Astronmembers\EmptyUsernameException;
use Backend\Exceptions\App\Memberkit\EmptyProductException;
use Backend\Models\AppAstronmembersIntegration;
use Backend\Models\Product;

class EditController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Astron Members integration';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/apps/astronmembers/editView.php';

    public function view(string $view_method, Request $request, array $params=[])
    {
        extract((array) $this);
        extract($params);

        $view = new View;

        try
        {
            $integration = AppAstronmembersIntegration::where('id', $id)->where('user_id', $user->id)->first();
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
        $username = $body->username ?? null;
        $password = $body->password ?? null;
        $clubid = $body->clubid ?? null;
        $product_id = $body->product_id ?? null;

        try
        {
            $integration = AppAstronmembersIntegration::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($integration)) throw new AppAstronmembersIntegrationNotFound;

            if (!$username) throw new EmptyUsernameException;
            if (!$password) throw new EmptyPasswordException;
            if (!$clubid) throw new EmptyClassidException;
            if (!$product_id) throw new EmptyProductException;

            $integration->status = $enabled;
            $integration->username = aes_encode_db($username);
            $integration->password = aes_encode_db($password);
            $integration->clubid = $clubid;
            $integration->product_id = $product_id;
            $integration->save();

            $response_data = new ResponseData(['status' => 'success', 'message' => __('AstronMembers API key updated successfully.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (AppAstronmembersIntegrationNotFound)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('AstronMembers integration not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyUsernameException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The api key is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyPasswordException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The api password is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyProductException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The product is blank.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        catch (EmptyClassidException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The class id cannot be blank.')]);
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
