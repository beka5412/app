<?php

namespace Backend\Controllers\User\Balance;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Enums\Order\EOrderStatus;
use Backend\Models\User;
use Backend\Models\Balance;
use Backend\Models\BalanceHistory;
use Backend\Models\BankAccount;
use Backend\Models\Withdrawal;
use Backend\Models\Order;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Balance';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/balance/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $withdrawal_fee = get_setting('withdrawal_fee');
        $minimum_withdrawal = get_setting('minimum_withdrawal');

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = get_current_route();

        $balance_history = BalanceHistory::where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $balance = Balance::where('user_id', $user->id)->first();
        $bank_account = BankAccount::where('user_id', $user->id)->first();
        $withdrawals = Withdrawal::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);

        $info = $balance_history;

        View::render($this->indexFile, compact('title', 'context', 'user', 'balance', 'bank_account', 'withdrawals', 'balance_history', 'info', 'url',
            'withdrawal_fee', 'minimum_withdrawal'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/balance';

        $balance_history = BalanceHistory::where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $balance = Balance::where('user_id', $user->id)->first();
        $bank_account = BankAccount::where('user_id', $user->id)->first();
        $withdrawals = Withdrawal::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);

        $info = $balance_history;

        View::response($this->indexFile, compact('title', 'context', 'user', 'balance', 'bank_account', 'withdrawals', 'balance_history', 'info', 'url'));
    }

    // public function full(Request $request)
    // {
    //     $title = $this->title;
    //     $context = $this->context;
    //     $user = $this->user;
    //     $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
    //     View::render($this->indexFile, compact('title', 'context', 'user', 'products'), ['no_js' => true]);
    // }
}