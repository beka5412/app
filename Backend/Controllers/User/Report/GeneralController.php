<?php

namespace Backend\Controllers\User\Report;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\Order;


class GeneralController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Relatorio Gelal';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/reports/GeneralController.php';
        $this->user = user(); // Obtém o usuário autenticado
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // Renderizando a view com os dados das vendas
        View::render($this->indexFile, compact('title', 'context', 'user'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // Aqui você pode adicionar a lógica que precisar, semelhante ao método index
        View::response($this->indexFile, compact('title', 'context', 'user'));
    }
}
