<?php

namespace Backend\Controllers\User\Customer;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Customer;

class ShowController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Show Customers';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/customers/showView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $customer = Customer::where('id', $id)->where('user_id', $user->id)
        ->with(['purchases' => function($query) {
            $query->with('product')->with('order');
        }])
        ->first();
        View::render($this->indexFile, compact('title', 'context', 'user', 'customer'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $customer = Customer::where('id', $id)->where('user_id', $user->id)
        ->with(['purchases' => function($query) {
            $query->with('product')->with('order');
        }])
        ->first();
        View::response($this->indexFile, compact('title', 'context', 'user', 'customer'));
    }
}