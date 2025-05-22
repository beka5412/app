<?php

namespace Backend\Controllers\Admin\Chargeback;

use Backend\App;
use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Chargeback;
use Backend\Http\Response;
use Backend\Models\PaylabChargebackAlert;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends AFrontendController
{
    use TFrontendController;

    public string $title = 'Chargebacks';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/admin/chargeback/indexView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/admin/chargebacks';
        $view = new View;

        try
        {
            $chargebacks = PaylabChargebackAlert::with('order')->orderBy('id', 'DESC')
                ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page)->onEachSide(2);

            $info = $chargebacks;
            
            $view = View::$view_method($this->indexFile, compact('title', 'context', 'admin', 'chargebacks', 'info', 'url'));
        }

        catch (ModelNotFoundException)
        {
            $notfound = new NotFoundController($this->application);
            $view = $notfound->element(new Request);
        }

        return $view;
    }
}
