<?php

namespace Backend\Controllers\Controller;

use Backend\Http\Request;

trait TFrontendController
{
    public function index(Request $request)
    {
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $pagination = compact('page', 'per_page');

        return $this->view('render', $request, $this->application->url_params, $pagination);
    }

    public function element(Request $request)
    {
        $params = (array) $request->pageParams();
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $pagination = compact('page', 'per_page');

        return $this->view('response', $request, $params, $pagination);
    }
}
