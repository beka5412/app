<?php

namespace Backend\Controllers\User\Recurrence;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Recurrence;
use Backend\Models\Invoice; // Importando o modelo Invoice



class ShowController
{
    public App $application;
    public $user;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Detalhes da Assinatura';
        $this->context = 'dashboard';
        $this->showFile = 'frontend/view/user/recurrence/showView.php';
        $this->user = user();
    }

    public function show(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // Buscar os detalhes da recorrência
        $recurrence = Recurrence::with(['order', 'customer', 'orderMeta', 'product'])
            ->where('id', $id)
            ->first();

        // Buscar as faturas relacionadas ao order_id
        $invoices = Invoice::where('order_id', $recurrence->order->id)
            ->orderBy('created_at', 'desc') // Ordenando da mais recente para a mais antiga
            ->get();


        // Renderizar a view com os dados da recorrência e faturas
        View::render($this->showFile, compact('title', 'context', 'user', 'recurrence', 'invoices'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
       
        View::response($this->indexFile, compact('title', 'context', 'user'));
    }
    
}
