<?php

namespace Backend\Controllers\User\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;


class SalesByDayController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Sales by Day';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/reports/salesByDayView.php';
        $this->user = user(); // Obtém o usuário autenticado
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // Definindo o número de itens por página
        $per_page = 10;

        // Obtendo a página atual
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Captura as datas do filtro (se existirem)
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

        // Construção da query base para as ordens aprovadas do usuário logado
        $query = Order::where('user_id', $user->id)
            ->where('status', 'approved');

        // Aplicando os filtros de data
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Agrupa por data e calcula a soma e contagem
        $salesData = $query->selectRaw('DATE(created_at) as date, COUNT(*) as total_orders, SUM(total) as total_value')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(perPage: $per_page, page: $page);

        // Renderizando a view com os dados das vendas
        View::render($this->indexFile, compact('title', 'context', 'user', 'salesData'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // Aqui você pode adicionar a lógica que precisar, semelhante ao método index
        View::response($this->indexFile, compact('title', 'context', 'user', 'salesData'));
    }
}
