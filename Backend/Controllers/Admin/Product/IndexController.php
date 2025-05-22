<?php

namespace Backend\Controllers\Admin\Product;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Product;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Products';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/products/indexView.php';
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

        $products = Product::orderBy('id', 'DESC')->with('user')
        ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $info = $products;
        View::render($this->indexFile, compact('context', 'title', 'admin', 'info', 'url', 'products'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $page = $request->query('page') ?: 1;

        $per_page = 10;
        $per_page = 10;
        $url = site_url().'/products';
        $products = Product::orderBy('id', 'DESC')->with('user')
        ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $info = $products;
        View::render($this->indexFile, compact('context', 'title', 'admin','info', 'url', 'products'));
    }
}