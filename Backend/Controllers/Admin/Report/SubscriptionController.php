<?php

namespace Backend\Controllers\Admin\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Settings;
use Backend\Http\Response;
use Backend\Models\Subscription;
use Backend\Models\Order;
use Backend\Models\User;
use Backend\Models\Customer;
use Backend\Models\Checkout;
use Backend\Models\Product;

class SubscriptionController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Relatório de Assinaturas Ativas';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/reports/activeSubscriptionsView.php'; // Define a view da página
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        
     
        View::render($this->indexFile, compact('context', 'title', 'admin'));
    }
    
    public function activeSubscriptions(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
    
        // Quantidade de itens por página
        $perPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $perPage;
    
        // Filtros
        $query = Subscription::where('status', 'active');
    
        // Filtro por data
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $startDate = $_GET['start_date'];
            $endDate = $_GET['end_date'];
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
    
        // Filtro por user_id
        if (!empty($_GET['user_id'])) {
            $userId = $_GET['user_id'];
            $query->whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
    
        // **Cálculo total de assinaturas e valor total considerando os dois filtros**
        if (!empty($_GET['start_date']) && !empty($_GET['end_date']) && !empty($_GET['user_id'])) {
            // Quando ambos data e user_id estão presentes
            $totalActiveSubscriptions = Subscription::where('status', 'active')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('order', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->count();
    
            $totalActiveValue = Order::whereIn('id', Subscription::where('status', 'active')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('order', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->pluck('order_id'))
                ->sum('total');
        } elseif (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            // Quando apenas a data está presente
            $totalActiveSubscriptions = Subscription::where('status', 'active')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
    
            $totalActiveValue = Order::whereIn('id', Subscription::where('status', 'active')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->pluck('order_id'))
                ->sum('total');
        } elseif (!empty($_GET['user_id'])) {
            // Quando apenas o user_id está presente
            $totalActiveSubscriptions = Subscription::where('status', 'active')
                ->whereHas('order', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->count();
    
            $totalActiveValue = Order::whereIn('id', Subscription::where('status', 'active')
                ->whereHas('order', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->pluck('order_id'))
                ->sum('total');
        } else {
            // Caso nenhum filtro seja selecionado
            $totalActiveSubscriptions = Subscription::where('status', 'active')->count();
            $totalActiveValue = Order::whereIn('id', Subscription::where('status', 'active')->pluck('order_id'))->sum('total');
        }
    
        // **Contagem total de assinaturas para calcular as páginas**
        $totalRecords = $query->count(); // Pegue o total de registros após aplicar os filtros
    
        // Calcular o total de páginas
        $totalPages = ceil($totalRecords / $perPage);
    
        // **Ordenar pela mais recente**
        $query->orderBy('created_at', 'desc');
    
        // Buscar assinaturas com limite e offset para paginação
        $subscriptions = $query->limit($perPage)->offset($offset)->get();
    
        $reportData = [];
    
        foreach ($subscriptions as $subscription) {
            $order = Order::find($subscription->order_id);
    
            if ($order) {
                $owner = User::find($order->user_id);
                $customer = Customer::find($order->customer_id);
    
                // Buscar o produto via checkout_id -> product_id -> product name
                $checkout = Checkout::find($order->checkout_id);
                $productName = 'Produto não encontrado';
                if ($checkout) {
                    $product = Product::find($checkout->product_id);
                    if ($product) {
                        $productName = $product->name;
                    }
                }
    
                // Definir o ciclo de cobrança
                $cycle = $subscription->interval_count . ' ' . $subscription->interval;
                if ($subscription->interval_count > 1) {
                    $cycle .= 's'; // Plural
                }
    
                $reportData[] = [
                    'owner_name' => $owner ? $owner->name : 'N/A',
                    'owner_email' => $owner ? $owner->email : 'N/A',
                    'customer_name' => $customer ? $customer->name : 'N/A',
                    'subscription_value' => $order->total,
                    'created_at' => $order->created_at,
                    'billing_cycle' => $cycle,
                    'product_name' => $productName
                ];
            }
        }
    
        // **Buscar apenas os usuários com assinaturas ativas**
        $userIdsWithActiveSubscriptions = Order::whereIn('id', Subscription::where('status', 'active')->pluck('order_id'))
            ->pluck('user_id')
            ->unique();
        $userNames = User::whereIn('id', $userIdsWithActiveSubscriptions)->pluck('name', 'id');
    
        // Renderizar a view com os dados do relatório e as variáveis totais
        View::render($this->indexFile, compact('title', 'context', 'admin', 'reportData', 'currentPage', 'totalPages', 'totalActiveSubscriptions', 'totalActiveValue', 'userNames'));
    }
    
    

}
