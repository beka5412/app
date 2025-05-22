<?php

namespace Backend\Controllers\User\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\Order;
use Backend\Models\Checkout;
use Backend\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;


class SalesByProductController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Vendas por produto';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/reports/salesByProductView.php';
        $this->user = user(); // Obtém o usuário autenticado
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // Filtros de data
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        // Filtro de produto
        $productId = $_GET['product_id'] ?? null;

        // Definindo o número de itens por página
        $per_page = 10;
        $page = $_GET['page'] ?? 1;

        // Construindo a query com a qualificação da tabela orders.user_id
        $query = Order::where('orders.user_id', $user->id)  // Qualificando a tabela 'orders' antes do campo 'user_id'
            ->where('orders.status', 'approved')
            ->join('checkouts', 'checkouts.id', '=', 'orders.checkout_id')
            ->join('products', 'products.id', '=', 'checkouts.product_id')
            ->selectRaw('DATE(orders.created_at) as sale_date, products.name as product_name, COUNT(*) as total_sold, SUM(orders.total) as total_value')
            ->groupBy('sale_date', 'products.name');

        // Aplicando o filtro de datas, se fornecido
        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        // Aplicando o filtro de produto, se fornecido
        if ($productId) {
            $query->where('products.id', $productId);
        }

        // Paginação
        $salesByProduct = $query->orderBy('sale_date', 'desc')
            ->paginate($per_page, ['*'], 'page', $page);

        // Filtrar apenas produtos do usuário logado
        $products = Product::join('checkouts', 'checkouts.product_id', '=', 'products.id')
            ->join('orders', 'orders.checkout_id', '=', 'checkouts.id')
            ->where('orders.user_id', $user->id)
            ->select('products.id', 'products.name')
            ->distinct()
            ->get();

        // Renderizando a view
        View::render($this->indexFile, compact('title', 'context', 'user', 'salesByProduct', 'products'));
    }
}


   