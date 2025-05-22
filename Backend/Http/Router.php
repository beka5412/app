<?php 

namespace Backend\Http;

use Backend\App;
use Backend\Http\Route;
use Backend\Controllers\Browser\NotFoundController;

class Router
{
    public App $application;
    
    use \Backend\Traits\Util;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    private function not_found()
    {
        $notfound = new NotFoundController($this->application);
        $notfound->view(new Request);
    }

    public function apply()
    {
        if ($subdomain = subdomain())
        {
            foreach ($this->subdomains() as $sub => $routes)
            {
                if ($subdomain == $sub)
                {
                    foreach ($routes as $route)
                    {
                        $verb = strtolower($route[0]);
                        $url = $route[1];
                        $method = $route[2];
                        $middleware = $route[3] ?? [];

                        $route = new Route($this->application);
                        $route->$verb($url, $method, $middleware);
                    }

                    // se nenhuma rota for encontrada
                    $this->not_found();

                    exit;
                }
            }

            // se nenhum subdominio for encontrado
            $this->not_found();
        }

        else 
        {
            foreach ($this->application->routes["."] as $route)
            {
                $verb = strtolower($route[0]);
                $url = $route[1];
                $method = $route[2];
                $middleware = $route[3] ?? [];

                $route = new Route($this->application);
                $route->$verb($url, $method, $middleware);
            }

            // se nenhuma rota for encontrada
            $this->not_found();
        }
        
    }
}