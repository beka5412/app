<?php

namespace Backend\Controllers\Admin\Customer;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\User;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Customers';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/customers/indexView.php';
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

        $clients = User::orderBy('id', 'DESC')
        ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $info = $clients;
        $total = User::count();
        View::render($this->indexFile, compact('context', 'title', 'admin','info', 'url', 'clients', 'total'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $page = $request->query('page') ?: 1;

        $per_page = 10;
        $url = site_url().'/customers';

        $clients = User::orderBy('id', 'DESC')
        ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $info = $clients;
        $total = User::count();
        View::render($this->indexFile, compact('context', 'title', 'admin','info', 'url', 'clients', 'total'));
    }
}