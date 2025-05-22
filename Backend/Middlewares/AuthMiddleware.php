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

class AuthMiddleware
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function login() : Bool
    {
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        $user = user();
        $request = new Request;
        
        if (empty($user))
        {
            if (strpos($content_type, 'application/json') !== false)
            {
                header("Error-Type: unauthorized");
                $login = new LoginController($this->application);
                $login->element(new Request);
            }

            else
            {
                try
                {
                    $link = new Link($this->application);
                    $link->to(site_url(), '/login');
                    Link::changeUrl(site_url(), '/login');
                }

                catch (NotFoundException $ex)
                {
                    $notfound = new NotFoundController($this->application);
                    $notfound->view($request);
                }
            }
            
            return false;
        }

        else 
        {
            if ($user->status == 'expired')
            {
                if (get_header('Client-Name') == 'Pager')
                {
                    header("Error-Type: unauthorized");
                    $login = new LoginController($this->application);
                    $login->element(new Request);
                }


                else
                    header("location: ".env('EXPIRED_USER_REDIRECT'));

                return false;
            }
        }

        return true;
    }
}