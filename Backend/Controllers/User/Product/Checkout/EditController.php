<?php

namespace Backend\Controllers\User\Product\Checkout;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Checkout;
use Backend\Exceptions\Checkout\EmptyNameException;
use Backend\Exceptions\Checkout\EmptyDarkModeException;
use Backend\Exceptions\Checkout\CheckoutNotFoundException;
use Backend\Models\Testimonial;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Editar checkout';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/checkouts/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $product_id, $checkout_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $checkout = Checkout::where('id', $checkout_id)->where('user_id', $user->id)->first();
        $testimonials = Testimonial::where('checkout_id', $checkout_id)->where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        View::render($this->indexFile, compact('title', 'context', 'user', 'checkout', 'product', 'testimonials'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $product_id = $body?->product_id;
        $checkout_id = $body?->checkout_id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('user_id', $user->id)->where('id', $product_id)->first();
        $checkout = Checkout::where('id', $checkout_id)->where('user_id', $user->id)->first();
        $testimonials = Testimonial::where('checkout_id', $checkout_id)->where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(10);

        View::response($this->indexFile, compact('title', 'context', 'user', 'checkout', 'product', 'testimonials'));
    }

    public function update(Request $request, $product_id, $checkout_id)
    {
        $user = $this->user;
        $response = [];
        $body = $request->json();
        $name = $body->name;
        $darkmode = $body->darkmode;
        $top_banner = $body->top_banner;
        $top_2_banner = $body->top_2_banner;
        $sidebar_banner = $body->sidebar_banner;
        $footer_banner = $body->footer_banner;
        $logo = $body->logo;
        $favicon = $body->favicon;
        $top_color = $body->top_color;
        $primary_color = $body->primary_color;
        $secondary_color = $body->secondary_color;
        $countdown_enabled = $body->countdown_enabled;
        $countdown_text = $body->countdown_text;
        $countdown_time = $body->countdown_time;
        $header_bg_color = $body->header_bg_color;
        $header_text_color = $body->header_text_color;
        $countdown_color = $body->countdown_color ?? '';
        $theme = $body->theme;
        $pix_enabled = $body->pix_enabled;
        $credit_card_enabled = $body->credit_card_enabled;
        $billet_enabled = $body->billet_enabled;
        $pix_discount_enabled = $body->pix_discount_enabled;
        $pix_discount_amount = $body->pix_discount_amount;
        $credit_card_discount_enabled = $body->credit_card_discount_enabled;
        $credit_card_discount_amount = $body->credit_card_discount_amount;
        $billet_discount_enabled = $body->billet_discount_enabled;
        $billet_discount_amount = $body->billet_discount_amount;
        $max_installments = $body->max_installments;
        $pix_thanks_page_enabled = $body->pix_thanks_page_enabled;
        $pix_thanks_page_url = $body->pix_thanks_page_url;
        $credit_card_thanks_page_enabled = $body->credit_card_thanks_page_enabled;
        $credit_card_thanks_page_url = $body->credit_card_thanks_page_url;
        $billet_thanks_page_enabled = $body->billet_thanks_page_enabled;
        $billet_thanks_page_url = $body->billet_thanks_page_url;
        $default = $body->default ?? '';
        $status = $body->status ?? '';
        $notification_interested24_number = $body->notification_interested24_number ?? '';
        $notification_interested_weekly_number = $body->notification_interested_weekly_number ?? '';
        $notification_order24_number = $body->notification_order24_number ?? '';
        $notification_order_weekly_number = $body->notification_order_weekly_number ?? '';
        $notification_interested24_enabled = $body->notification_interested24_enabled ?? '';
        $notification_interested_weekly_enabled = $body->notification_interested_weekly_enabled ?? '';
        $notification_order24_enabled = $body->notification_order24_enabled ?? '';
        $notification_order_weekly_enabled = $body->notification_order_weekly_enabled ?? '';
        $whatsapp_number = preg_replace("/\D/", "", $body->whatsapp_number ?? '');

        try
        {
            // desativa outros checkouts desse produto (apenas se o atual estiver sendo ativado)
            if ($default)
                $checkouts = Checkout::where('user_id', $user->id)->where('product_id', $product_id)->update(['default' => 0]);

            $checkout = Checkout::where('id', $checkout_id)->where('product_id', $product_id)->where('user_id', $user->id)->first();

            if (empty($checkout)) throw new CheckoutNotFoundException;
            if (!$name) throw new EmptyNameException;
            if (is_null($darkmode)) throw new EmptyDarkModeException;

            $checkout->name = $name;
            $checkout->dark_mode = $darkmode;
            $checkout->top_banner = $top_banner;
            $checkout->sidebar_banner = $sidebar_banner;
            $checkout->footer_banner = $footer_banner;
            $checkout->top_2_banner = $top_2_banner;
            $checkout->logo = $logo;
            $checkout->favicon = $favicon;
            $checkout->top_color = $top_color;
            $checkout->primary_color = $primary_color;
            $checkout->secondary_color = $secondary_color;
            $checkout->countdown_enabled = $countdown_enabled;
            $checkout->countdown_text = $countdown_text;
            $checkout->countdown_time = $countdown_time;
            $checkout->header_bg_color = $header_bg_color;
            $checkout->header_text_color = $header_text_color;
            if ($countdown_color) $checkout->countdown_color = $countdown_color;
            $checkout->checkout_theme_id = $theme;
            $checkout->pix_enabled = $pix_enabled;
            $checkout->credit_card_enabled = $credit_card_enabled;
            $checkout->billet_enabled = $billet_enabled;
            $checkout->pix_discount_enabled = $pix_discount_enabled;
            $checkout->pix_discount_amount = $pix_discount_amount;
            $checkout->credit_card_discount_enabled = $credit_card_discount_enabled;
            $checkout->credit_card_discount_amount = $credit_card_discount_amount;
            $checkout->billet_discount_enabled = $billet_discount_enabled;
            $checkout->billet_discount_amount = $billet_discount_amount;
            $checkout->max_installments = $max_installments;
            $checkout->pix_thanks_page_enabled = $pix_thanks_page_enabled;
            $checkout->pix_thanks_page_url = $pix_thanks_page_url;
            $checkout->credit_card_thanks_page_enabled = $credit_card_thanks_page_enabled;
            $checkout->credit_card_thanks_page_url = $credit_card_thanks_page_url;
            $checkout->billet_thanks_page_enabled = $billet_thanks_page_enabled;
            $checkout->billet_thanks_page_url = $billet_thanks_page_url;
            $checkout->default = $default;
            $checkout->status = $status;
            $checkout->notification_interested24_number = $notification_interested24_number;
            $checkout->notification_interested_weekly_number = $notification_interested_weekly_number;
            $checkout->notification_order24_number = $notification_order24_number;
            $checkout->notification_order_weekly_number = $notification_order_weekly_number;
            $checkout->notification_interested24_enabled = $notification_interested24_enabled;
            $checkout->notification_interested_weekly_enabled = $notification_interested_weekly_enabled;
            $checkout->notification_order24_enabled = $notification_order24_enabled;
            $checkout->notification_order_weekly_enabled = $notification_order_weekly_enabled;
            $checkout->whatsapp_number = $whatsapp_number;
            $checkout->save();

            $response = ["status" => "success", "message" => "Checkout atualizado com sucesso."];
        }

        catch (CheckoutNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Checkout não encontrado."];
        }

        catch (EmptyNameException $ex)
        {
            $response = ["status" => "error", "message" => "O nome do checkout não pode estar em branco."];
        }

        catch (EmptyDarkModeException $ex)
        {
            $response = ["status" => "error", "message" => "Selecione se o darkmode vai ser habilitado ou não."];
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * Upload checkout image
     * 
     * @access public
     * @param \Backend\Http\Request $request    Request object
     * @param Mixed $checkout_id                Checkout ID
     * @param Mixed $product_id                 Product ID
     * @throws \Backend\Exceptions\Product\InvalidImageException
     * @throws \Backend\Exceptions\Product\CheckoutNotFoundException
     */
    public function uploadImage(Request $request, $product_id, $checkout_id)
    {
        $user = $this->user;
        $response = [];

        $filename = uniqid() . ".png";

        $source = $_FILES['image']['tmp_name'] ?? '';
        $destination = base_path('frontend/public/upload') . "/" . $filename;

        try
        {
            $check = getimagesize($source);
            $mime = $check['mime'] ?? '';
            $is_image = strlen($mime) > 0 && strpos($mime, "image/") >= 0;
            if (!$is_image) throw new InvalidImageException;

            $checkout = Checkout::where('id', $checkout_id)->where('product_id', $product_id)->where('user_id', $user->id)->first();
            if (empty($checkout)) throw new CheckoutNotFoundException;

            $uploaded_image = move_uploaded_file($source, $destination);

            $response = ['status' => 'success', 'message' => "Imagem carregada com sucesso.", "data" => ["image" => "/upload/$filename"]];
        }

        catch (InvalidImageException $ex)
        {
            $response = ['status' => 'error', 'message' => "Este arquivo não é uma imagem válida."];
        }

        catch (CheckoutNotFoundException $ex)
        {
            $response = ['status' => 'error', 'message' => "Você não tem permissão para subir esta imagem."];
        }

        finally
        {
            Response::json($response);
        }
    }
}
