<?php

namespace Backend\Controllers\Admin\Catalog;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Edit Catalog';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/catalogs/editView.php';
        $this->admin = admin();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        View::render($this->indexFile, compact('context', 'title', 'admin'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        
        $body = $request->pageParams();
        $id = $body?->id;

        View::response($this->indexFile, compact('context', 'title', 'admin'));
    }
}