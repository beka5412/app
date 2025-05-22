<?php

namespace Backend\Controllers\User\Chat;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Chat;
use Backend\Models\Exceptions\Chat\ChatNotFoundException;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Kyc';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/chats/indexView.php';
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

        $chats = Chat::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $chats;

        View::render($this->indexFile, compact('title', 'context', 'user', 'chats', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/chats';

        $chats = Chat::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $info = $chats;

        View::response($this->indexFile, compact('title', 'context', 'user', 'chats', 'info', 'url'));
    }

    public function new(Request $request)
    {
        $user = $this->user;

        $chat = new Chat;
        $chat->user_id = $user->id;
        $chat->name = 'Chat #'.time();
        $chat->save();

        Response::json(["message" => "Chat criado com sucesso.", "id" => $chat->id]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $chat = Chat::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($chat)) throw new ChatNotFoundException;

            $chat->delete();

            $response = ["status" => "success", "message" => "Chat deletado com sucesso."];
        }

        catch(ChatNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Chat não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }
}