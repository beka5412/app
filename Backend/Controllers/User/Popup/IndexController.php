<?php

namespace Backend\Controllers\User\Popup;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Popup;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Kyc';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/popups/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = get_current_route();

        $popups = Popup::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $popups;

        View::render($this->indexFile, compact('title', 'context', 'user', 'popups', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/popups';

        $popups = Popup::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $popups;

        View::response($this->indexFile, compact('title', 'context', 'user', 'popups', 'info', 'url'));
    }

    public function new(Request $request)
    {
        $user = $this->user;

        $popup = new Popup;
        $popup->user_id = $user->id;
        $popup->name = 'Popup #'.time();
        $popup->save();

        Response::json(["message" => "Popup criado com sucesso.", "id" => $popup->id]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $popup = Popup::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($popup)) throw new PopupNotFoundException;

            $popup->delete();

            $response = ["status" => "success", "message" => "Popup deletado com sucesso."];
        }

        catch(PopupNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Popup não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }
}