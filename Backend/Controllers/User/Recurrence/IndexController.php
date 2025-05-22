<?php

namespace Backend\Controllers\User\Recurrence;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Recurrence;
use Backend\Models\Subscription; // Importando o modelo Subscription
use Backend\Models\Order; // Importando o modelo Order

class IndexController
{
    public App $application;
    public $user;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Recurrence Index';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/recurrence/indexView.php';
        $this->user = user();
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

   
        // Buscando as assinaturas do usuário logado com paginação
        $recurrences = Recurrence::with(['order', 'customer', 'orderMeta', 'product'])
            ->whereHas('order', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        // Renderizando a view com as assinaturas paginadas
        View::render($this->indexFile, compact('title', 'context', 'user', 'recurrences'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        // Definindo o número de itens por página
        $per_page = 10;

        // Obtendo a página atual
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

   
        // Buscando as assinaturas do usuário logado com paginação
        $recurrences = Recurrence::with(['order', 'customer', 'orderMeta', 'product'])
            ->whereHas('order', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        // Renderizando a view com as assinaturas paginadas
        View::render($this->indexFile, compact('title', 'context', 'user', 'recurrences'));
    }

}
