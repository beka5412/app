<?php

namespace Backend\Controllers\User\Upsell;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Upsell;
use Backend\Exceptions\Product\ProductNotFoundException;
use Backend\Enums\Upsell\EUpsellStatus;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Upsell';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/upsells/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $upsells = Upsell::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::render($this->indexFile, compact('title', 'context', 'user', 'upsells'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $upsells = Upsell::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        View::response($this->indexFile, compact('title', 'context', 'user', 'upsells'));
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $upsell = Upsell::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($upsell)) throw new UpsellNotFoundException;

            $upsell->delete();

            $response = ["status" => "success", "message" => "Upsell deletado com sucesso."];
        }

        catch(UpsellNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Upsell não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function new(Request $request)
    {
        $user = $this->user;

        $upsell = new Upsell;
        $upsell->user_id = $user->id;
        $upsell->name = 'Rascunho #'.time();
        $upsell->status = EUpsellStatus::DRAFT;
        $upsell->save();

        Response::json(["message" => "Upsell criado com sucesso.", "id" => $upsell->id]);
    }

    public function get_template(Request $request)
    {
        $data = json_decode(urldecode($request->query('data')), true);

        View::template("frontend/view/templates/upsell/buttonView.php", $data);
        View::template("frontend/view/templates/upsell/baseView.php", $data);
    }
}