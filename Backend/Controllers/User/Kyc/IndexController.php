<?php

namespace Backend\Controllers\User\Kyc;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Kyc;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Kyc';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/kyc/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $kyc = Kyc::where('user_id', $user->id)->first();
        View::render($this->indexFile, compact('title', 'context', 'user', 'kyc'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $kyc = Kyc::where('user_id', $user->id)->first();
        View::response($this->indexFile, compact('title', 'context', 'user', 'kyc'));
    }

    public function new(Request $request)
    {
        $user = $this->user;

        $kyc = new Kyc;
        $kyc->user_id = $user->id;
        $kyc->name = 'Kyc #'.time();
        $kyc->save();

        Response::json(["message" => "Chat criado com sucesso.", "id" => $kyc->id]);
    }
}