<?php

namespace Backend\Controllers\Admin\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Order;
use Backend\Models\User;
use Backend\Models\Customer;
use Backend\Models\Checkout;
use Backend\Models\Product;

class DailySalesController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Relatório de Vendas Diárias';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/reports/dailySalesView.php';
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        // Quantidade de itens por página
        $perPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $perPage;

        // Filtros
        $query = Order::where('status', 'approved');

        // Filtro por data inicial e final
        if (!empty($_GET['start_date'])) {
            $startDate = $_GET['start_date'];
            $query->where('created_at', '>=', $startDate);
        }
        if (!empty($_GET['end_date'])) {
            $endDate = $_GET['end_date'];
            $query->where('created_at', '<=', $endDate);
        }

        // Filtro por user_id (vendedor)
        if (!empty($_GET['user_id'])) {
            $userId = $_GET['user_id'];
            $query->where('user_id', $userId);
        }

        // Filtro por produto
        if (!empty($_GET['product_id'])) {
            $productId = $_GET['product_id'];
            $query->whereHas('checkout', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        // Filtro por ID do pedido, e-mail ou nome do cliente
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('email', 'LIKE', "%$search%")
                          ->orWhere('name', 'LIKE', "%$search%");
                    });
            });
        }

        // Contar o total de registros filtrados
        $filteredTotal = $query->count();

        // Paginação
        $sales = $query->orderBy('created_at', 'desc')->limit($perPage)->offset($offset)->get();

        // Calcular total de pedidos e valor total com filtro
        $totalSales = $filteredTotal;
        $totalRevenue = $query->sum('total');

        // Mostrar visão geral se não houver filtros
        if (empty($_GET['start_date']) && empty($_GET['end_date']) && empty($_GET['user_id']) && empty($_GET['product_id']) && empty($_GET['search'])) {
            $totalSales = Order::where('status', 'approved')->count();
            $totalRevenue = Order::where('status', 'approved')->sum('total');
        }

        // Buscar apenas vendedores e produtos com vendas
        $userIdsWithSales = Order::where('status', 'approved')->pluck('user_id')->unique();
        $userNames = User::whereIn('id', $userIdsWithSales)->pluck('name', 'id');

        $productIdsWithSales = Checkout::whereIn('id', Order::where('status', 'approved')->pluck('checkout_id'))->pluck('product_id');
        $productNames = Product::whereIn('id', $productIdsWithSales)->pluck('name', 'id');

        // Formatar os dados das vendas para exibir na view
        $salesData = [];
        foreach ($sales as $sale) {
            $customer = Customer::find($sale->customer_id);
            $checkout = Checkout::find($sale->checkout_id);
            $productName = 'Produto não encontrado';
            if ($checkout) {
                $product = Product::find($checkout->product_id);
                if ($product) {
                    $productName = $product->name;
                }
            }
            $salesData[] = [
                'transaction_id' => $sale->id,
                'seller_name' => User::find($sale->user_id)->name ?? 'N/A',
                'customer_name' => $customer->name ?? 'N/A',
                'sale_date' => $sale->created_at,
                'sale_value' => $sale->total,
                'gateway' => $sale->gateway ?? 'N/A',
                'product_name' => $productName
            ];
        }

        // Gerar a paginação
        $pagination = $this->renderPagination($currentPage, ceil($filteredTotal / $perPage));

        // Renderizar a view
        View::render($this->indexFile, compact('title', 'context', 'admin', 'salesData', 'totalSales', 'totalRevenue', 'userNames', 'productNames', 'pagination'));
    }

    // Método para gerar a paginação compactada
    protected function renderPagination($currentPage, $totalPages)
    {
        $html = '<ul class="pagination justify-content-center">';
        if ($totalPages > 1) {
            // Primeira página
            if ($currentPage > 1) {
                $html .= '<li class="page-item"><a class="page-link" href="?page=1">&laquo;</a></li>';
                $html .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage - 1) . '">&lt;</a></li>';
            }

            // Páginas anteriores
            for ($i = max(1, $currentPage - 2); $i < $currentPage; $i++) {
                $html .= '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
            }

            // Página atual
            $html .= '<li class="page-item active"><a class="page-link" href="?page=' . $currentPage . '">' . $currentPage . '</a></li>';

            // Páginas seguintes
            for ($i = $currentPage + 1; $i <= min($currentPage + 2, $totalPages); $i++) {
                $html .= '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
            }

            // Última página
            if ($currentPage < $totalPages) {
                $html .= '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage + 1) . '">&gt;</a></li>';
                $html .= '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">&raquo;</a></li>';
            }
        }
        $html .= '</ul>';
        return $html;
    }
}
