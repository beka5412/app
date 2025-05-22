<?php

namespace Backend\Controllers\User\Coupon;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Coupon;
use Backend\Exceptions\Coupon\CouponNotFoundException;
use Backend\Enums\Coupon\ECouponStatus;
use Backend\Enums\Coupon\ECouponType;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Coupons';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/coupons/indexView.php';
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
        
        $coupons = Coupon::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page)->onEachSide(2);

        $info = $coupons;

        View::render($this->indexFile, compact('title', 'context', 'user', 'coupons', 'info', 'url'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/coupons';

        $coupons = Coupon::where('user_id', $user->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page)->onEachSide(2);

        $info = $coupons;

        View::response($this->indexFile, compact('title', 'context', 'user', 'coupons', 'info', 'url'));
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        try
        {
            $coupon = Coupon::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($coupon)) throw new CouponNotFoundException;

            $coupon->delete();

            $response = ["status" => "success", "message" => "Cupom deletado com sucesso."];
        }

        catch(CouponNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Cupom não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function new(Request $request)
    {
        $user = $this->user;

        $coupon = new Coupon;
        $coupon->user_id = $user->id;
        $coupon->code = time().'OFF';
        $coupon->discount = '50';
        $coupon->description = null;
        $coupon->status = ECouponStatus::DRAFT;
        $coupon->type = ECouponType::PERCENT;
        $coupon->save();

        Response::json(["message" => "Cupom criado com sucesso.", "id" => $coupon->id]);
    }
}