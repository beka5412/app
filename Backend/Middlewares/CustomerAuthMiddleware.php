<?php 

namespace Backend\Middlewares;

use Backend\App;
use Backend\Http\Link;
use Backend\Http\Request;
use Backend\Exceptions\NotFoundException;
use Backend\Exceptions\User\ExpiredUserException;
use Backend\Controllers\Subdomains\Purchase\Login;
use Backend\Controllers\Browser\NotFoundController;

class CustomerAuthMiddleware
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function login() : Bool
    {
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        $customer = customer();
        $request = new Request;
        
        if (empty($customer))
        {
            if (strpos($content_type, 'application/json') !== false)
            {
                header("Error-Type: unauthorized");
                $login = new Login\IndexController($this->application);
                $login->element(new Request);
            }

            else
            {
                try
                {
                    $link = new Link($this->application);
                    $link->to(get_subdomain_serialized('purchase'), '/login');
                    Link::changeUrl(get_subdomain_serialized('purchase'), '/login');
                }

                catch (NotFoundException $ex)
                {
                    $notfound = new NotFoundController($this->application);
                    $notfound->view($request);
                }
            }
            
            return false;
        }

        // else 
        // {
        //     if ($user->status == 'expired')
        //     {
        //         if (get_header('Client-Name') == 'Pager')
        //         {
        //             header("Error-Type: unauthorized");
        //             $login = new LoginController($this->application);
        //             $login->element(new Request);
        //         }


        //         else
        //             header("location: ".env('EXPIRED_USER_REDIRECT'));

        //         return false;
        //     }
        // }

        return true;
    }
}