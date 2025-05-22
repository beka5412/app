<?php

namespace Backend\Controllers\Admin\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Settings;
use Backend\Http\Response;


class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Relatorios';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/reports/indexView.php'; // Define a view da página
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        
     
        View::render($this->indexFile, compact('context', 'title', 'admin'));
    }
    
    public function update()
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        
     
        View::render($this->indexFile, compact('context', 'title', 'admin'));
    }
    
    
}