<?php

namespace Backend\Controllers\User\Sale;

use Backend\App;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Order;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Sales';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/sales/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $search = $request->query('search') ?: '';
        $date = $request->query('date') ?: '';
        $product = $request->query('product') ?: '';
        $approved = $request->query('approved') ?: '';
        $pending = $request->query('pending') ?: '';
        $cancelled = $request->query('cancelled') ?: '';

        $per_page = 10;
        $url = get_current_route();

        $orders = Order::orderBy('id', 'DESC')
            ->with(['new_meta', 'get_user', 'customer'])
            ->where('status_details', '<>', EOrderStatusDetail::REJECTED->value)
            ->when($approved === 'on' || $pending === 'on' || $cancelled === 'on', function ($query) use ($approved, $pending, $cancelled) {
                $query->where(function ($q) use ($approved, $pending, $cancelled) {
                    if ($approved === 'on') {
                        $q->orWhere('status', EOrderStatus::APPROVED->value);
                    }
                    if ($pending === 'on') {
                        $q->orWhere('status', EOrderStatus::PENDING->value);
                    }
                    if ($cancelled === 'on') {
                        $q->orWhere('status', EOrderStatus::CANCELED->value);
                    }
                });
            }, function ($query) {
                $query->where('status', '<>', EOrderStatus::INITIATED->value);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('customer', function ($q) use ($search) {
                        $q->where('email', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    })
                        ->orWhere('id', $search);
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', 'LIKE', "%{$date}%");
            })
            ->when($product, function ($query) use ($product) {
                if (!empty($product) && $product !== 'Selecione uma opção') {
                    $query->whereHas('new_meta', function ($q) use ($product) {
                        $q->where('name', 'product_id')
                            ->where('value', 'LIKE', "%{$product}%");
                    });
                }
            })
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('aff_id', $user->id);
            })
        ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $orders;
        $products = Product::where('user_id', $user->id)->get();
        
        View::render($this->indexFile, compact('title', 'context', 'user', 'products', 'orders', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $search = $request->query('search') ?: '';
        $date = $request->query('date') ?: '';
        $product = $request->query('product') ?: '';
        $approved = $request->query('approved') ?: '';
        $pending = $request->query('pending') ?: '';
        $cancelled = $request->query('cancelled') ?: '';

        $per_page = 10;
        $url = site_url().'/sales';

        $orders = Order::orderBy('id', 'DESC')
            ->with(['new_meta', 'get_user', 'customer'])
            ->where('status_details', '<>', EOrderStatusDetail::REJECTED->value)
            ->when($approved === 'on' || $pending === 'on' || $cancelled === 'on', function ($query) use ($approved, $pending, $cancelled) {
                $query->where(function ($q) use ($approved, $pending, $cancelled) {
                    if ($approved === 'on') {
                        $q->orWhere('status', EOrderStatus::APPROVED->value);
                    }
                    if ($pending === 'on') {
                        $q->orWhere('status', EOrderStatus::PENDING->value);
                    }
                    if ($cancelled === 'on') {
                        $q->orWhere('status', EOrderStatus::CANCELED->value);
                    }
                });
            }, function ($query) {
                $query->where('status', '<>', EOrderStatus::INITIATED->value);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('customer', function ($q) use ($search) {
                        $q->where('email', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    })
                        ->orWhere('id', $search);
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', 'LIKE', "%{$date}%");
            })
            ->when($product, function ($query) use ($product) {
                if (!empty($product) && $product !== 'Selecione uma opção') {
                    $query->whereHas('new_meta', function ($q) use ($product) {
                        $q->where('name', 'product_id')
                            ->where('value', 'LIKE', "%{$product}%");
                    });
                }
            })
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('aff_id', $user->id);
            })
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $orders;

        View::response($this->indexFile, compact('title', 'context', 'user', 'orders', 'info', 'url'));
    }
}