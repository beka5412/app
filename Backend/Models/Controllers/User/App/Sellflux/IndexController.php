<?php

namespace Backend\Controllers\User\App\Sellflux;

use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Exceptions\App\Sellflux\AppSellfluxIntegrationNotFound;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AppSellfluxIntegration;
use Backend\Template\View;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Sellflux integrations';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/apps/sellflux/indexView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/app/sellflux';
        $view = new View;

        try
        {
            $integrations = AppSellfluxIntegration::with('product')->where('user_id', $user->id)->orderBy('id', 'DESC')
                ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page)->onEachSide(2);

            $info = $integrations;
            
            $view = View::$view_method($this->indexFile, compact('title', 'context', 'user', 'integrations', 'info', 'url'));
        }

        catch (ModelNotFoundException)
        {
            $notfound = new NotFoundController($this->application);
            $view = $notfound->element(new Request);
        }

        return $view;
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;

        try
        {
            $integration = AppSellfluxIntegration::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($integration)) throw new AppSellfluxIntegrationNotFound;

            $integration->delete();

            $response_data = new ResponseData(['status' => 'success', 'message' => __('Sellflux integration deleted successfully.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch(AppSellfluxIntegrationNotFound)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('Sellflux integration not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }

    public function new(): Response
    {
        $user = $this->user;

        $integration = new AppSellfluxIntegration;
        $integration->user_id = $user->id;
        $integration->status = 0;
        $integration->save();

        return Response::json(['message' => __('Integration created successfully.'), 'id' => $integration->id]);
    }
}
