<?php

namespace Backend\Controllers\User\Product\Plan;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Models\Plan;
use Backend\Models\Product;
use Backend\Exceptions\Plan\PlanNotFoundException;
use Backend\Exceptions\Plan\SlugAlreadyExistsException;
use Backend\Exceptions\Product\ProductNotFoundException;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Editar plano';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/plans/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id, $plan_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        try
        {
            $plan = Plan::where('id', $plan_id)->where('user_id', $user->id)->first();
            if (empty($plan)) throw new PlanNotFoundException;

            $product = Product::where('id', $product_id)->where('user_id', $user->id)->first();
            if (empty($product)) throw new ProductNotFoundException;

            View::render($this->indexFile, compact('title', 'context', 'user', 'plan', 'product'));
        }

        catch (ModelNotFoundException|PlanNotFoundException|ProductNotFoundException)
        {
            $link = new Link($this->application);
            $link->to(site_url(), "/product/$product_id/plans");
            Link::changeUrl(site_url(), "/product/$product_id/plans");
        }
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $product_id = $body?->product_id;
        $plan_id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        try
        {
            $plan = Plan::where('id', $plan_id)->where('user_id', $user->id)->first();
            if (empty($plan)) throw new PlanNotFoundException;

            $product = Product::where('id', $product_id)->where('user_id', $user->id)->first();
            if (empty($product)) throw new ProductNotFoundException;
    
            View::response($this->indexFile, compact('title', 'context', 'user', 'plan', 'product'));
        }

        catch (ModelNotFoundException|PlanNotFoundException|ProductNotFoundException)
        {
            $link = new Link($this->application);
            $link->to(site_url(), "/product/$product_id/plans");
            Link::changeUrl(site_url(), "/product/$product_id/plans");
        }
    }

    /**
     * Atualizar o plano
     *
     * @param Request $request
     * @param int|null $product_id
     * @param int|null $id
     * @throws PlanNotFoundException
     * @throws ProductNotFoundException
     * @throws SlugAlreadyExistsException
     * @return void
     */
    public function update(Request $request, $product_id, $id) : void
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $name = $body->name;
        $price = $body->price;
        // $slug = $body->slug;
        $recurrence_period = $body->recurrence_period;

        try
        {
            $plan = Plan::where('id', $id)->where('user_id', $user->id)->first();
            $product = Product::where('id', $product_id)->first();

            if (empty($plan)) throw new PlanNotFoundException;
            if (empty($product)) throw new ProductNotFoundException;
            // if (checkout_slug_exists($product_id, $slug)) throw new SlugAlreadyExistsException;
            // if (!$slug) $slug = strtoupper(uniqid());

            $plan->name = $name;
            $plan->price = $price;
            // $plan->slug = $slug;
            
            switch ($recurrence_period)
            {
                case 'daily': 
                    $plan->recurrence_interval = 'day'; 
                    $plan->recurrence_interval_count = 1; 
                    break;
            
                case 'monthly': 
                    $plan->recurrence_interval = 'month'; 
                    $plan->recurrence_interval_count = 1; 
                    break;
                    
                case 'bimonthly': 
                    $plan->recurrence_interval = 'month'; 
                    $plan->recurrence_interval_count = 2; 
                    break;
                    
                case 'quarterly': 
                    $plan->recurrence_interval = 'month'; 
                    $plan->recurrence_interval_count = 3; 
                    break;
                    
                case 'biannual': 
                    $plan->recurrence_interval = 'month'; 
                    $plan->recurrence_interval_count = 6; 
                    break;
                    
                case 'yearly': 
                    $plan->recurrence_interval = 'year'; 
                    $plan->recurrence_interval_count = 1; 
                    break;
            }

            $plan->save();

            $response = ["status" => "success", "message" => "Plano atualizado com sucesso."];
        }

        catch (PlanNotFoundException)
        {
            $response = ["status" => "error", "message" => "Plano não encontrado."];
        }

        catch (ProductNotFoundException)
        {
            $response = ["status" => "error", "message" => "O produto referente a este plano não existe."];
        }

        catch (SlugAlreadyExistsException)
        {
            $response = ["status" => "error", "message" => "Você já registrou este slug! Por favor, tente outro."];
        }

        finally
        {
            Response::json($response);
        }
    }
}