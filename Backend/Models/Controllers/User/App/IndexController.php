<?php

namespace Backend\Controllers\User\App;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Models\AppUtmifyIntegration;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;

class IndexController
{
    public App $application;
    public string $title = 'Apps';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/apps/indexView.php';
    public User $user;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = get_current_route();

        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $products;
        $utmify = AppUtmifyIntegration::where('user_id', $user->id)->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'products', 'info', 'url', 'utmify'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/apps';

        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $products;
        $utmify = AppUtmifyIntegration::where('user_id', $user->id)->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'products', 'info', 'url', 'utmify'));
    }

    public function full(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::render($this->indexFile, compact('title', 'context', 'user', 'products'), ['no_js' => true]);
    }
}