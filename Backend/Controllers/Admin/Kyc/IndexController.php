<?php

namespace Backend\Controllers\Admin\Kyc;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Kyc;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Withdrawal';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/kyc/indexView.php';
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = get_current_route();

        $kycs = Kyc::orderBy('id', 'DESC')->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $kycs;

        View::render($this->indexFile, compact('title', 'context', 'admin', 'kycs', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/admin/kyc';

        $kycs = Kyc::orderBy('id', 'DESC')->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $kycs;

        View::response($this->indexFile, compact('title', 'context', 'admin', 'kycs', 'info', 'url'));
    }
}