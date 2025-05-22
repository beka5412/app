<?php

namespace Backend\Controllers\Browser;

use Backend\App;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Http\Request;
use Backend\Exceptions\ClassNotFoundException;

class NotFoundController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Not Found';
        $this->user = user();
        $this->content_type = $_SERVER['CONTENT_TYPE'] ?: 'text/html';
    }

    public function default()
    {
        echo "
        <h1>404 Not Found</h1>        
        ";
    }

    public function view(Request $request)
    {
        $title = $this->title;
        $user = $this->user;
        $content_type = $this->content_type;
        $subdomain = subdomain();
        $subdomain_pascalcase = pascal_case($subdomain);

        header("Content-Type: $content_type");
        header("HTTP/1.1 404 Not Found");

        if (strpos($content_type, 'text/html') !== false)
        {
            if ($subdomain)
            {
                try
                {
                    if (!empty($user))
                    {
                        $class = "\Backend\Controllers\Browser\Subdomains\\$subdomain_pascalcase\Dashboard\NotFoundController";
                        if (!class_exists($class)) throw new ClassNotFoundException;
                        $notfound = new $class($this->application);
                        $notfound->view($request);
                    }

                    else
                    {
                        $class = "\Backend\Controllers\Browser\Subdomains\\$subdomain_pascalcase\Public\NotFoundController";
                        if (!class_exists($class)) throw new ClassNotFoundException;
                        $notfound = new $class($this->application);
                        $notfound->view($request);
                    }
                }

                catch (ClassNotFoundException $ex)
                {
                    $this->default();
                }
            }
            else
            {
                if (!empty($user))
                {
                    $notfound = new \Backend\Controllers\Browser\Dashboard\NotFoundController($this->application);
                    $notfound->view($request);
                }

                else
                {
                    $notfound = new \Backend\Controllers\Browser\Public\NotFoundController($this->application);
                    $notfound->view($request);
                }
            }
        }

        else if ($content_type == 'application/json')
            echo json_encode(["code" => "404", "message" => "Not found"]);
            
        else echo "404 Not Found";
        
        return new View;
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $user = $this->user;
        // $content_type = $this->content_type;
        
        header("Error-Type: not_found");
       
        if (!empty($user))
        {
            $notfound = new \Backend\Controllers\Browser\Dashboard\NotFoundController($this->application);
            $notfound->element($request);
        }

        else
        {
            $notfound = new \Backend\Controllers\Browser\Public\NotFoundController($this->application);
            $notfound->element($request);
        }

        return new View;
    }

    public function render(Request $request)
    {
        return $this->view($request);
    }

    public function response(Request $request)
    {
        return $this->element($request);
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }
}