<?php

namespace Backend\Controllers\User;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\User;

class ProfileController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Perfil';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/profileView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::render($this->indexFile, compact('title', 'context', 'user'));
    }
    
    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::response($this->indexFile, compact('title', 'user', 'context'));
    }
}