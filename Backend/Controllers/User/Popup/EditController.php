<?php

namespace Backend\Controllers\User\Popup;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Popup;
use Backend\Models\Product;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Exceptions\Popup\{ PopupNotFoundException, EmptyNameException };

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Edit Popup';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/popups/editView.php';
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
            $popup = Popup::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($popup)) throw new ModelNotFoundException;

            View::render($this->indexFile, compact('title', 'context', 'user', 'popup', 'products'));
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
            $popup = Popup::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($popup)) throw new ModelNotFoundException;

            View::response($this->indexFile, compact('title', 'context', 'user', 'popup', 'products'));
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
            $popup = Popup::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($popup)) throw new PopupNotFoundException;
            if (!$name) throw new EmptyNameException;

            $popup->name = $name;
            $popup->save();

            $response = ["status" => "success", "message" => "Popup atualizado com sucesso."];
        }

        catch(PopupNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Popup não encontrado."];
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