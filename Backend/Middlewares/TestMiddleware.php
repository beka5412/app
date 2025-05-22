<?php 

namespace Backend\Middlewares;

use Backend\App;
use Backend\Http\Link;
use Backend\Http\Request;
use Backend\Models\User;
use Backend\Exceptions\NotFoundException;
use Backend\Exceptions\User\ExpiredUserException;
use Backend\Controllers\Public\LoginController;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Http\Response;

class TestMiddleware
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function access() : Bool
    {
        $check = env('ENVIRONMENT') == 'development';
        
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        $request = new Request;

        if (!$check)
        {
            if (strpos($content_type, 'application/json') !== false)
            {
                header("Error-Type: unauthorized");
                $login = new LoginController($this->application);
                $login->element(new Request);
            }

            else
            {
                Response::redirect(site_url().'/login');
            }
        }

        return $check;
    }
}
