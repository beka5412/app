<?php 

namespace Backend\Middlewares\Subdomains\Checkout;

use Backend\App;
use Backend\Http\Link;
use Backend\Http\Route;
use Backend\Http\Request;
use Backend\Models\User;
use Backend\Exceptions\NotFoundException;
use Backend\Controllers\Public\LoginController;
use Backend\Controllers\Browser\NotFoundController;

class RedirectMiddleware
{
    public App $application;
    public $instance;

    public function __construct(App $application, $instance, $info)
    {
        $this->application = $application;
        $this->instance = $instance;
        $this->info = $info;
    }

    /**
     * Sempre que digitar uma rota na url e ela for encontrada na lista, continuar o fluxo normal
     * caso nao encontre, executar o metodo informado
     * 
     * Detalhes:
     * Tudo que esta escrito na URI eh considerado um SKU e para que se consiga criar
     * rotas que nao sao sku, este middleware precisa existir.
     * 
     * Exemplo:
     * checkout.site.com/ABC123
     * 
     * Isso vai abrir um checkout.
     * 
     * Por conta disso, nao seria possivel ver outra pagina, pois tudo escrito apos a / seria considerado um
     * SKU e como nao seria encontrado, a pagina padrao que seria renderizado eh a de 404.
     * 
     * Para que exista uma pagina como "checkout.site.com/pix" este metodo eh necessario.
     *
     * @param Request $request
     * @return bool
     */
    public function boot(Request $request) : bool
    {
        // checar se existe uma rota com o valor em REQUEST_URI
        foreach ($this->application->routes[subdomain()] ?? [] as $route)
        {
            $verb = strtolower($route[0]);
            $url = $route[1];
            $method = $route[2];
            $middleware = $route[3] ?? [];

            // web.php == REQUEST_URI
            if ($url == $this->info->url)
                continue; // pula a url que der match

            // rederiza pagina excecao
            $route = new Route($this->application);
            $route->$verb(
                $url, 
                $method, 
                // remove este middleware da lista para que nao caia em um loop infinito
                array_filter($this->info->middlewares, fn($middleware) => $middleware <> 'Backend\Middlewares\Subdomains\Checkout\RedirectMiddleware@boot')
            );
        }

        return true;
    }
}