<?php 

namespace Backend;

use Backend\Http\Router;
use Pdp\Rules as DomainSuffixReader;

class App
{
    public $routes = [];
    public $allowedVerbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    public $url_params = [];

    public function defaultHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: ".join(",", $this->allowedVerbs));

        if (request_method() == "OPTIONS")
        {
            header("HTTP/1.1 200 OK");
            die();
        }
    }

    public function attributeRoute($routes)
    {
        $all_classes = array_keys(include base_path('vendor/composer/autoload_classmap.php'));
        $class_map = array_filter(
            $all_classes,
            fn($class) => strpos($class, 'Backend') === 0 && strpos($class, 'Backend\Attributes') === false
        );
        // $list_attributes = array_filter($all_classes, fn($class) => strpos($class, 'Backend\Attributes') === 0);

        foreach ($class_map as $class)
        {
            $reflection = new \ReflectionClass($class);
            foreach ($reflection->getMethods() as $method)
            {
                $attributes = $method->getAttributes();
                $method_name = $method->getName();
                
                if (count($attributes) > 0)
                {
                    foreach ($attributes as $attribute)
                    {
                        $attr_name = $attribute->getName();
                        $arguments = $attribute->getArguments();
                        if ($attr_name == 'Backend\Attributes\Route')
                        {
                            $verb = strtoupper($arguments['verb'] ?? '');
                            $uri = $arguments['uri'] ?? '';
                            $subdomain = $arguments['subdomain'] ?? '';

                            $route = [$verb, $uri, "$class@$method_name"];
                            if (!$subdomain)
                                $routes["."][] = $route;
                            else
                                $routes[$subdomain][] = $route;
                        }
                    }
                }
            }
        }
        
        return $routes;
    }

    public function defaultDate()
    {
        date_default_timezone_set(env('TIMEZONE'));
    }

    public function __construct()
    {
        $this->routes = $this->attributeRoute(include base_path('routes/web.php'));
        $GLOBALS['domainPublicSuffixList'] = DomainSuffixReader::fromPath(base_path("storage/domains/suffix.dat"));

        // $GLOBALS['translate'] = json_decode(join("\n", file(base_path("lang/".env("LANG").".json"))));
        $cookie_lang = ''; // TODO: pegar idioma da preferencia do usuario
        $lang = $cookie_lang ? $cookie_lang : get_setting('lang'); // 'es_MX';        
        $GLOBALS['translate'] = json_decode(join("\n", file(base_path("lang/".$lang.".json"))));

        // session_name('ROCKETPAYS_SES');
        // session_set_cookie_params(0, '/', '.' . get_host());
        session_start();

        $this->defaultDate();
        $this->defaultHeaders();
        
        $router = new Router($this);
        $router->apply();
    }
}