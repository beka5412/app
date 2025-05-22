<?php

namespace Backend\Controllers\User\Product\Plan;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Exceptions\Plan\EmptyPlanListException;
use Backend\Exceptions\Product\EmptyProductListException;
use Backend\Enums\Product\ERecurrenceInterval;
use Backend\Models\Plan;
use Backend\Models\Product;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Plan';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/plans/indexView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
    
        try
        {
            $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
            if (empty($product)) throw new EmptyProductListException;

            $plans = Plan::where('user_id', $user->id)->where('product_id', $product_id)->orderBy('id', 'DESC')->paginate(10);
            if (empty($plans)) throw new EmptyPlanListException;

            View::render($this->indexFile, compact('title', 'context', 'user', 'plans', 'product'));
        }

        catch (ModelNotFoundException|EmptyPlanListException|EmptyProductListException)
        {
            $link = new Link($this->application);
            $link->to(site_url(), "/product/$product_id/plans");
            Link::changeUrl(site_url(), "/product/$product_id/plans");
        }
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $body = $request->pageParams();
        $product_id = $body->id;
        
        try
        {
            $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
            if (empty($product)) throw new EmptyProductListException;

            $plans = Plan::where('user_id', $user->id)->where('product_id', $product_id)->orderBy('id', 'DESC')->paginate(10);
            if (empty($plans)) throw new EmptyPlanListException;

            View::response($this->indexFile, compact('title', 'context', 'user', 'plans', 'product'));
        }

        catch (ModelNotFoundException|EmptyPlanListException|EmptyProductListException)
        {
            $link = new Link($this->application);
            $link->to(site_url(), "/product/$product_id/plans");
            Link::changeUrl(site_url(), "/product/$product_id/plans");
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $plan = Plan::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($plan)) throw new PlanNotFoundException;

            $plan->delete();

            $response = ["status" => "success", "message" => "Plano deletado com sucesso."];
        }

        catch(PlanNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Plano não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function new(Request $request, $product_id)
    {
        $user = $this->user;

        $plan = new Plan;
        $plan->product_id = $product_id;
        $plan->user_id = $user->id;
        $plan->price = 49;
        $plan->name = "Draft #".time();
        $plan->slug = strtoupper(uniqid());
        $plan->recurrence_interval = ERecurrenceInterval::MONTH->value;
        $plan->recurrence_interval_count = 1;
        $plan->save();

        Response::json(["message" => "Plano criado com sucesso.", "id" => $plan->id]);
    }
}