<?php

namespace Backend\Controllers\User\App\Cademi;

use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Exceptions\App\Cademi\AppCademiIntegrationNotFound;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AppCademiIntegration;
use Backend\Template\View;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Cademi integrations';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/apps/cademi/indexView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/app/cademi';
        $view = new View;

        try
        {
            $integrations = AppCademiIntegration::where('user_id', $user->id)->orderBy('id', 'DESC')
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
            $integration = AppCademiIntegration::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($integration)) throw new AppCademiIntegrationNotFound;

            $integration->delete();

            $response_data = new ResponseData(['status' => 'success', 'message' => __('Cademi integration deleted successfully.')]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch(AppCademiIntegrationNotFound)
        {
            $response_data = new ResponseData(['status' => 'error', 'message' => __('Cademi integration not found.')]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status);
    }

    public function new(): Response
    {
        $user = $this->user;

        $integration = new AppCademiIntegration;
        $integration->user_id = $user->id;
        $integration->status = 0;
        $integration->save();

        return Response::json(['message' => __('Integration created successfully.'), 'id' => $integration->id]);
    }
}
