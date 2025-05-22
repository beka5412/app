<?php

namespace Backend\Controllers\User\App\UTMify;

use Backend\App;
use Backend\Exceptions\App\Utmify\AppUtmifyIntegrationNotFoundException;
use Backend\Exceptions\App\Utmify\EmptyApikeyException;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AppUtmifyIntegration;
use Backend\Models\User;
use Backend\Template\View;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Exception;

class EditController
{
    public App $application;
    public string $title = 'Apps';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/apps/utmify/indexView.php';
    public User $user;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $utmify = AppUtmifyIntegration::where('user_id', $user->id)->first();

        return View::render($this->indexFile, compact('title', 'context', 'user', 'utmify'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $utmify = AppUtmifyIntegration::where('user_id', $user->id)->first();

        return View::response($this->indexFile, compact('title', 'context', 'user', 'utmify'));
    }

    public function update(Request $request)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $apikey = $body->apikey ?? 0;

        try
        {
            $utmify = AppUtmifyIntegration::where('user_id', $user->id)->first();

            if (empty($utmify))
            {
                $utmify = new AppUtmifyIntegration;
                $utmify->user_id = $user->id;
                $utmify->status = 1;
            }

            if (!$apikey) throw new EmptyApikeyException;

            $utmify->apikey = $apikey;
            $utmify->save();

            $response_data = new ResponseData(['status' => 'success', 'message' => __('UTMify API key updated successfully.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (EmptyApikeyException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('The api key is blank.')]);
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

    public function change(Request $request)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $status = $body->status ?? 0;

        try
        {
            $utmify = AppUtmifyIntegration::where('user_id', $user->id)->first();

            if (empty($utmify)) throw new AppUtmifyIntegrationNotFoundException;

            $utmify->status = $status;
            $utmify->save();

            $response_data = new ResponseData([
                'status' => 'success', 
                'message' => $status 
                    ? __('Integration activated! It is now possible to follow the result on UTMify.')
                    : __('UTMify integration disabled.')
            ]);
            $response_status = new ResponseStatus('200 OK');
        }
       
        catch (AppUtmifyIntegrationNotFoundException)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('You haven\'t set your api key yet.')]);
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
