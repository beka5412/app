<?php

namespace Backend\Controllers\Admin\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Order;
use Backend\Models\User;

class DailySalesByUserController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Relatório de Vendas Diárias por Usuário';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/reports/dailySalesByUserView.php'; // Define a view da página
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        // Filtros de data
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $userId = $_GET['user_id'] ?? null;

        // Buscar vendas agrupadas por usuário e data
        $query = Order::selectRaw('user_id, DATE(created_at) as sale_date, COUNT(id) as total_orders, SUM(total) as total_revenue')
            ->where('status', 'approved');

        // Se a data inicial e final forem fornecidas, aplicar o filtro
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        // Filtro por user_id (vendedor)
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $salesData = $query->groupBy('user_id', 'sale_date')
            ->orderBy('sale_date', 'desc')
            ->get();

        // Buscar apenas usuários que já realizaram vendas
        $userIdsWithSales = Order::where('status', 'approved')->pluck('user_id')->unique();
        $userNames = User::whereIn('id', $userIdsWithSales)->pluck('name', 'id');

        // Formatando os dados para exibir na view
        $formattedSales = [];
        foreach ($salesData as $sale) {
            $user = User::find($sale->user_id);
            $formattedSales[] = [
                'user_name' => $user ? $user->name : 'Usuário não encontrado',
                'sale_date' => $sale->sale_date,
                'total_orders' => $sale->total_orders,
                'total_revenue' => $sale->total_revenue,
            ];
        }

        // Renderizar a view
        View::render($this->indexFile, compact('title', 'context', 'admin', 'formattedSales', 'startDate', 'endDate', 'userNames', 'userId'));
    }
}
