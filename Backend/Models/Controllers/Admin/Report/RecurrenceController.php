<?php

namespace Backend\Controllers\Admin\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Invoice;
use Backend\Models\Order;
use Backend\Models\User;
use Backend\Models\Customer;
use Backend\Models\Checkout;
use Backend\Models\Product;

class RecurrenceController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Relatório de Recorrências';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/reports/recurrencesView.php'; // Define a view da página
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        // Filtros
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $userId = $_GET['user_id'] ?? null;
        $productId = $_GET['product_id'] ?? null;
        $search = $_GET['search'] ?? null;
        $perPage = 10; // Quantidade de itens por página
        $currentPage = $_GET['page'] ?? 1;

        // Query básica para buscar faturas pagas (paid = 1), ordenadas por mais recentes
        $query = Invoice::where('paid', 1)
            ->orderBy('paid_at', 'desc'); // Ordena da mais recente para a mais antiga

        // Filtro por data de pagamento
            if ($startDate) {
                $startDate = date('Y-m-d 00:00:00', strtotime($startDate)); // Define o horário inicial como 00:00:00
                $query->where('paid_at', '>=', $startDate);
            }
            if ($endDate) {
                $endDate = date('Y-m-d 23:59:59', strtotime($endDate)); // Define o horário final como 23:59:59
                $query->where('paid_at', '<=', $endDate);
            }

        // Filtro por user_id (vendedor)
        if ($userId) {
            $query->whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Filtro por product_id
        if ($productId) {
            $query->whereHas('order.checkout', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        // Filtro por nome do cliente ou ID do pedido
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search) // Busca pelo ID da invoice
                    ->orWhereHas('order.customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                          ->orWhere('email', 'LIKE', "%$search%");
                    });
            });
        }

        // Calcular o total de faturas e o valor total das faturas com base nos filtros
        $totalInvoices = $query->count(); // Conta o total de faturas filtradas
        $totalValue = 0; // Soma o valor total das faturas (baseado na coluna 'total' de orders)

        // Certificar que $totalValue tem um valor válido
        $totalValue = $totalValue ?? 0;

        // Paginação (com total de páginas e offset)
        $invoices = $query->skip(($currentPage - 1) * $perPage)->take($perPage)->get();

        // BUSCAR APENAS VENDEDORES QUE POSSUEM PEDIDOS (USER_ID EM ORDERS)
        $userIdsWithSales = Order::pluck('user_id')->unique();
        $userNames = User::whereIn('id', $userIdsWithSales)->pluck('name', 'id');

        // BUSCAR APENAS PRODUTOS QUE JÁ FORAM VENDIDOS (PRODUCT_ID EM CHECKOUTS)
        $productIdsWithSales = Checkout::pluck('product_id')->unique();
        $productNames = Product::whereIn('id', $productIdsWithSales)->pluck('name', 'id');

        // Formatando os dados para exibir na view
        $formattedInvoices = [];
        foreach ($invoices as $invoice) {
            $order = Order::find($invoice->order_id); // Relacionamento correto com a tabela orders
            if ($order) {
                $user = User::find($order->user_id); // Vendedor (user_id) na tabela orders
                $customer = Customer::find($order->customer_id); // Cliente (customer_id) na tabela orders
                $checkout = Checkout::find($order->checkout_id); // Checkout (checkout_id) na tabela orders
                $productName = 'Produto não encontrado';

                // Busca pelo nome do produto usando a tabela checkouts e products
                if ($checkout) {
                    $product = Product::find($checkout->product_id);
                    if ($product) {
                        $productName = $product->name;
                    }
                }

                // Montagem do array de invoices formatadas
                $formattedInvoices[] = [
                    'customer_name' => $customer ? $customer->name : 'Cliente não encontrado',
                    'user_name' => $user ? $user->name : 'Vendedor não encontrado',
                    'product_name' => $productName,
                    'paid_at' => $invoice->paid_at,
                    'order_id' => $order->id,
                    'created_order' => $order->created_at,
                    'total' => $order->total, // Valor total da ordem (presente na tabela orders)
                    'invoice_id' => $invoice->id,
                ];
            }
        }

        // Calcular o total de páginas para a paginação
        $totalPages = ceil($totalInvoices / $perPage);

        // Gerar a URL de paginação com os filtros
        $paginationQueryParams = http_build_query([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user_id' => $userId,
            'product_id' => $productId,
            'search' => $search
        ]);

        // Gerar paginação com filtros mantidos e exibição de 3 páginas + setas
        $maxPages = 3;
        $pagination = [
            'total_pages' => $totalPages,
            'current_page' => $currentPage,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage - 1,
            'next_page' => $currentPage + 1,
            'pagination_query_params' => $paginationQueryParams,
            'start_page' => max(1, $currentPage - floor($maxPages / 2)),
            'end_page' => min($totalPages, $currentPage + floor($maxPages / 2)),
        ];

        // Renderizar a view com os nomes de vendedores, produtos e informações de cards
        View::render($this->indexFile, compact(
            'title', 'context', 'admin', 'formattedInvoices',
            'startDate', 'endDate', 'userNames', 'productNames',
            'userId', 'productId', 'search', 'pagination', 'totalInvoices', 'totalValue'
        ));
    }
}
