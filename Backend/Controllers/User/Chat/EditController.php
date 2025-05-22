<?php

namespace Backend\Controllers\User\Chat;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Chat;
use Backend\Models\Product;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Exceptions\Chat\{ ChatNotFoundException, EmptyNameException };

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Edit Chat';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/chats/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $products = Product::where('user_id', $user->id)->get();

        try
        {
            $chat = Chat::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($chat)) throw new ModelNotFoundException;

            View::render($this->indexFile, compact('title', 'context', 'user', 'chat', 'products'));
        }

        catch (ModelNotFoundException $ex)
        {
            $link = new Link($this->application);
            $link->to(site_url(), '/chats');
            Link::changeUrl(site_url(), '/chats');
        }
    }

    public function element(Request $request)
    {
        $user = $this->user;
        
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;

        $products = Product::where('user_id', $user->id)->get();

        try
        {
            $chat = Chat::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($chat)) throw new ModelNotFoundException;

            View::response($this->indexFile, compact('title', 'context', 'user', 'chat', 'products'));
        }

        catch (ModelNotFoundException $ex)
        {
            $notfound = new NotFoundController($this->application);
            $notfound->element($request);
        }
    }

    public function update(Request $request, $id)
    {
        $user = $this->user;

        $response = [];
        $body = $request->json();
        $name = $body?->name ?? '';

        $response = [];

        try
        {
            $chat = Chat::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($chat)) throw new ChatNotFoundException;
            if (!$name) throw new EmptyNameException;

            $chat->name = $name;
            $chat->save();

            $response = ["status" => "success", "message" => "Chat atualizado com sucesso."];
        }

        catch(ChatNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Chat não encontrado."];
        }

        catch(EmptyNameException $ex)
        {
            $response = ["status" => "error", "message" => "O nome não pode estar em branco."];
        }

        finally
        {
            Response::json($response);
        }
    }
}