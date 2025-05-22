<?php

namespace Backend\Controllers\User\Domain;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Services\RunCloud\RunCloud;
use Backend\Exceptions\Domain\AddDomainException;
use Backend\Models\User;
use Backend\Models\Domain;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Domains';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/domain/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::render($this->indexFile, compact('title', 'context', 'user'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        View::response($this->indexFile, compact('title', 'context', 'user'));
    }

    /**
     * Cria um novo alias de dominio na RunCloud e salva no banco de dados
     *
     * @param Request $request
     * @throws AddDomainException
     * @return void
     */
    public function add(Request $request)
    {
        $user = $this->user;
        $runcloud = new RunCloud;
        $body = $request->json();
        $domain = $body->domain ?? '';
        $full_domain = "pay.$domain";
        $response = [];

        try
        {
            $add_domain = json_decode($runcloud->addDomain($full_domain) ?: '{}');
            
            if (empty($add_domain?->id)) throw new AddDomainException;

            $new_domain = new Domain;
            $new_domain->domain = $domain;
            $new_domain->full_domain = $full_domain;
            $new_domain->user_id = $user->id;
            $new_domain->save();

            $response = ["status" => "success", "message" => "Domínio adicionado com sucesso."];
        }

        catch(AddDomainException $ex)
        {
            $response = ["status" => "error", "message" => "Não foi possível adicionar este domínio."];
        }

        finally
        {
            Response::json($response);
        }
    }
}