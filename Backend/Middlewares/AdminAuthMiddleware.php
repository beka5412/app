<?php 

namespace Backend\Middlewares;

use Backend\App;
use Backend\Http\Link;
use Backend\Http\Request;
use Backend\Exceptions\NotFoundException;
use Backend\Controllers\Admin\Login;
use Backend\Controllers\Browser\NotFoundController;

class AdminAuthMiddleware
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function login() : Bool
    {
        $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
        $customer = admin();
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
                    $link->to(site_url(), '/admin/login');
                    Link::changeUrl(site_url(), '/admin/login');
                }

                catch (NotFoundException $ex)
                {
                    $notfound = new NotFoundController($this->application);
                    $notfound->view($request);
                }
            }
            
            return false;
        }

        return true;
    }
}