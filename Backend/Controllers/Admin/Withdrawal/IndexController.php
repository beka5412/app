<?php

namespace Backend\Controllers\Admin\Withdrawal;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Withdrawal;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Withdrawal';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/withdrawals/indexView.php';
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $withdrawals = Withdrawal::with([ 
            'user' => function($query) {
                return $query->with(['bank_account' => function($query) {
                    $query->with('bank');
                }]); 
            }
        ])
        ->orderBy('id', 'DESC')->paginate(10);
        $w_fee = doubleval(get_setting('withdrawal_fee'));
        
        View::render($this->indexFile, compact('context', 'title', 'admin', 'withdrawals', 'w_fee'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $withdrawals = Withdrawal::with([ 
            'user' => function($query) {
                return $query->with(['bank_account' => function($query) {
                    $query->with('bank');
                }]); 
            }
        ])
        ->orderBy('id', 'DESC')->paginate(10);
        $w_fee = doubleval(get_setting('withdrawal_fee'));
        View::response($this->indexFile, compact('context', 'title', 'admin', 'withdrawals', 'w_fee'));
    }
}