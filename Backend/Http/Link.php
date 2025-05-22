<?php 

namespace Backend\Http;

use Backend\App;
use Backend\Http\Request;
use Backend\Serializers;
use Backend\Exceptions\NotFoundException;

class Link
{
    public App $application;

    use \Backend\Traits\Util;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function to($domain, $to)
    {
        $request = new Request;

        if ($subdomain = subdomain())
        {
            foreach ($this->subdomains() as $sub => $routes)
            {
                if ($subdomain == $sub)
                {
                    foreach ($routes as $route)
                    {
                        $verb = $route[0];
                        $url = $route[1];
                        $namespace_method = $route[2];
                        $middleware = $route[3] ?? [];
                        
                        $aux = explode("@", $namespace_method);
                        $class = $aux[0];
                        $method = $aux[1];
            
                        $serializer = new Serializers\Url($url, $to);
                        $params = $serializer->getParams();
            
                        if ($serializer->match())
                        {
                            $instance = new $class($this->application);                
                            $instance->$method($request, ...$params);
                            return;
                        }
                    }

                    // se nenhuma rota for encontrada
                    throw new NotFoundException;
                    return;
                }
            }

            // se nenhum subdominio for encontrado
            throw new NotFoundException;
            return;
        }
 
        else 
        {
            foreach ($this->application->routes["."] as $route)
            {
                $verb = $route[0];
                $url = $route[1];
                $namespace_method = $route[2];
                $middleware = $route[3] ?? [];
                
                $aux = explode("@", $namespace_method);
                $class = $aux[0];
                $method = $aux[1];
    
                $serializer = new Serializers\Url($url, $to);
                $params = $serializer->getParams();
    
                if ($serializer->match())
                {
                    $instance = new $class($this->application);                
                    $instance->$method($request, ...$params);
                    return;
                }
            }

            // se nenhuma rota for encontrada
            throw new NotFoundException;
            return;
        }
    }

    public static function changeUrl($domain, $url)
    {
        echo "
        <script id=\"__changeUrl__\">
            window.history.pushState({ url: '$url', domain: '$domain' }, '', '$url');
            __changeUrl__.parentNode.removeChild(__changeUrl__);
        </script>
        ";
    }
}