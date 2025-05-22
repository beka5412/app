<?php

namespace Backend\Controllers\Admin\Order;

use Backend\App;
use Backend\Enums\Order\EOrderStatus;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Invoice;
use Backend\Models\Order;
use Backend\Models\Product;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Orders';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/orders/indexView.php';
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        $page = $request->query('page') ?: 1;
        $search = $request->query('search') ?: '';
        $date = $request->query('date') ?: '';
        $product = $request->query('product') ?: '';
        $approved = $request->query('approved') ?: '';
        $pending = $request->query('pending') ?: '';
        $cancelled = $request->query('cancelled') ?: '';

        $per_page = 10;
        $url = get_current_route() . '';

        $orders = Order::orderBy('id', 'DESC')
            ->with(['new_meta', 'get_user', 'customer'])
            ->when($approved === 'on' || $pending === 'on' || $cancelled === 'on', function ($query) use ($approved, $pending, $cancelled) {
                if ($approved === 'on') {
                    $query->orWhere('status', EOrderStatus::APPROVED->value);
                }
                if ($pending === 'on') {
                    $query->orWhere('status', EOrderStatus::PENDING->value);
                }
                if ($cancelled === 'on') {
                    $query->orWhere('status', EOrderStatus::CANCELED->value);
                }
            }, function ($query) {
                $query->whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value,EOrderStatus::CANCELED->value]);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('get_user', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                })->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
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
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $orders;

        $total = Order::whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value, EOrderStatus::CANCELED->value])->sum('total');
        $count_total = Order::whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value, EOrderStatus::CANCELED->value])->count();
        $approved = Order::where('status', EOrderStatus::APPROVED->value)->sum('total');
        $count_approved = Order::where('status', EOrderStatus::APPROVED->value)->count();
        $pending = Order::where('status', EOrderStatus::PENDING->value)->sum('total');
        $count_pending = Order::where('status', EOrderStatus::PENDING->value)->count();
        $cancel = Order::where('status', EOrderStatus::CANCELED->value)->sum('total');
        $count_cancel = Order::where('status', EOrderStatus::CANCELED->value)->count();
        $products = Product::all();

        $invoices_paid = 0;

        $total += $invoices_paid;
        $approved += $invoices_paid;

        // $orders_pending;
        // $approved_pay;
        // $separate_product;
        // $invoiced;
        // $delivered;
        // $canceled;
        // $on_carriage;

        View::render($this->indexFile, compact('context', 'title', 'admin', 'info', 'url', 'products', 'orders', 'total', 'count_total', 'approved', 'count_approved', 'pending', 'count_pending', 'cancel', 'count_cancel'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        $page = $request->query('page') ?: 1;
        $search = $request->query('search') ?: '';
        $date = $request->query('date') ?: '';
        $product = $request->query('product') ?: '';
        $approved = $request->query('approved') ?: '';
        $pending = $request->query('pending') ?: '';
        $cancelled = $request->query('cancelled') ?: '';

        $per_page = 10;
        $url = site_url() . '/admin/orders' . $search ? '?search=' . $search : '';

        $orders = Order::orderBy('id', 'DESC')
            ->with(['new_meta', 'get_user', 'customer'])
            ->when($approved === 'on' || $pending === 'on' || $cancelled === 'on', function ($query) use ($approved, $pending, $cancelled) {
                if ($approved === 'on') {
                    $query->orWhere('status', EOrderStatus::APPROVED->value);
                }
                if ($pending === 'on') {
                    $query->orWhere('status', EOrderStatus::PENDING->value);
                }
                if ($cancelled === 'on') {
                    $query->orWhere('status', EOrderStatus::CANCELED->value);
                }
            }, function ($query) {
                $query->whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value,EOrderStatus::CANCELED->value]);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('get_user', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                })->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
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
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $orders;


        $total = Order::whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value, EOrderStatus::CANCELED->value])->sum('total');
        $count_total = Order::whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value, EOrderStatus::CANCELED->value])->count();
        $approved = Order::where('status', EOrderStatus::APPROVED->value)->sum('total');
        $count_approved = Order::where('status', EOrderStatus::APPROVED->value)->count();
        $pending = Order::where('status', EOrderStatus::PENDING->value)->sum('total');
        $count_pending = Order::where('status', EOrderStatus::PENDING->value)->count();
        $cancel = Order::where('status', EOrderStatus::CANCELED->value)->sum('total');
        $count_cancel = Order::where('status', EOrderStatus::CANCELED->value)->count();
        $products = Product::where('approved', 1);

        $invoices_paid = 0;

        $total += $invoices_paid;
        $approved += $invoices_paid;

        View::response($this->indexFile, compact('context', 'title', 'admin', 'info', 'url', 'products', 'orders', 'total', 'count_total', 'approved', 'count_approved', 'pending', 'count_pending', 'cancel', 'count_cancel'));
    }

    public function status(Request $request, $status)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $page = $request->query('page') ?: 1;

        $per_page = 10;
        $url = get_current_route();

        $orders = Order::orderBy('id', 'DESC')->where('status', $status)->with('new_meta')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $info = $orders;

        $total = Order::whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value, EOrderStatus::CANCELED->value])->sum('total');
        $count_total = Order::whereIn('status', [EOrderStatus::PENDING->value, EOrderStatus::APPROVED->value, EOrderStatus::CANCELED->value])->count();
        $approved = Order::where('status', EOrderStatus::APPROVED->value)->sum('total');
        $count_approved = Order::where('status', EOrderStatus::APPROVED->value)->count();
        $pending = Order::where('status', EOrderStatus::PENDING->value)->sum('total');
        $count_pending = Order::where('status', EOrderStatus::PENDING->value)->count();
        $cancel = Order::where('status', EOrderStatus::CANCELED->value)->sum('total');
        $count_cancel = Order::where('status', EOrderStatus::CANCELED->value)->count();
        $products = Product::where('approved', 1);

        $invoices_paid = 0;

        $total += $invoices_paid;
        $approved += $invoices_paid;

        View::render($this->indexFile, compact('context', 'title', 'admin', 'info', 'url', 'products', 'orders', 'total', 'count_total', 'approved', 'count_approved', 'pending', 'count_pending', 'cancel', 'count_cancel'));
    }
}
