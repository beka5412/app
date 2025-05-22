<?php

namespace Backend\Http;

use Backend\App;
use Backend\Serializers;
use Backend\Http\Request;
use Backend\Controllers\Browser\MethodNotAllowedController;
use Backend\Controllers\Browser\NotFoundController;

class Route
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    /**
     * Encontra o controller para a solicitada
     *
     * @param string $verb           Verbo da lista de rotas
     * @param mixed $url             URL da lista de rotas
     * @param string $method         Metodo a ser executado da lista de rotas
     * @param array|null $config     Informacoes complementares
     * @return void
     */
    public function run($verb, $url, $method, ?Array $config=[])
    {
        $serializer = new Serializers\Url($url);
        $params = $serializer->getParams();
        $param_keys = $serializer->getParamKeys();
        
        // verifica se a url solicita eh a mesma iterada na lista de rotas
        // e se o verbo tambem eh o mesmo
        // $verb == "" verifica se foi escolhido o ANY
        if ($serializer->match() && ($verb == "" || request_method() == $verb))
        {
            $aux = explode("@", $method);
            $class = $aux[0];
            $method = $aux[1];
            $instance = new $class($this->application);
            $request = new Request;
            $aux = null;
            $info = (Object) [
                'verb' => $verb,
                'url' => $url,
                'request_url' => $serializer->request_url,
                'class' => $class,
                'method' => $method
            ];
            
            $this->application->url_params = array_combine($param_keys, $params);

            if (!empty($config))
            {
                $config = (Object) $config;

                if (!empty($config->middlewares))
                {
                    $info->middlewares = $config->middlewares;
                    foreach ($config->middlewares ?? [] as $middleware)
                    {
                        $aux = explode("@", $middleware);
                        $m_class = $aux[0];
                        $m_method = $aux[1];
                        
                        $m_instance = new $m_class($this->application, $instance, $info);
                        $result = $m_instance->$m_method($request);
                        if (!$result) exit; // se nao passou nos requisitos, morre e executa o que estiver no middleware
                    }
                }
            }

            $this->checkMethod($verb, function() use ($instance, $method, $request, $params) {
                header("Content-Type: text/html");
                header("HTTP/1.1 200 OK");
                
                try
                {
                    $instance->$method($request, ...$params);
                }

                catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex)
                {
                    $not_found = new NotFoundController($this->application);

                    if (client_name() == 'Pager')
                        $not_found->element($request);
                    else
                        $not_found->view($request);
                }
                
            });

            exit;
        }
    }

    public function checkMethod($verb, $callback)
    {
        $request_method = $_SERVER['REQUEST_METHOD'] ?? '';

        if ($request_method == $verb || $verb == '')
            $callback();

        else
        {
            if (request_method() == 'OPTIONS')
            {
                if (in_array(get_header('Access-Control-Request-Method'), $this->application->allowedVerbs))
                    header("HTTP/1.1 200 OK");
                else
                {
                    $method_not_allowed = new MethodNotAllowedController($this->application);
                    $method_not_allowed->view(new Request);
                }
            }

            else 
            {
                $method_not_allowed = new MethodNotAllowedController($this->application);
                $method_not_allowed->view(new Request);
            }
            exit;
        }
    }

    public function any(...$arguments)
    {
        $this->run('', ...$arguments);
    }

    public function get(...$arguments)
    {
        $this->run('GET', ...$arguments);
    }

    public function post(...$arguments)
    {
        $this->run('POST', ...$arguments);
    }

    public function put(...$arguments)
    {
        $this->run('PUT', ...$arguments);
    }

    public function patch(...$arguments)
    {
        $this->run('PATCH', ...$arguments);
    }

    public function delete(...$arguments)
    {
        $this->run('DELETE', ...$arguments);
    }
}