<?php

namespace Backend\Controllers\Cronjobs;
use Backend\Models\AbandonedCart;
use Backend\Notifiers\Email\Mailer as Email;
use Backend\Models\User;
use Backend\Models\Checkout;
use Backend\Models\Product;
use Backend\Models\Order;
use Backend\Models\Customer;

class AbandonedController
{
    public function alive()
    {
        $carts = AbandonedCart::where('is_abandoned', 0)->get();

        foreach ($carts as $cart)
        {
            if (strtotime($cart->last_update.' + 2 minutes') < time())
            {
                $customer = Customer::where('email', $cart->email)->first();
                if (!empty($customer))
                {
                    // checar se nao existe uma compra a partir da data do abandono
                    // para nao enviar mensagem de carrinho abandonado caso a pessoa tenha comprado
                    $order = Order::where('checkout_id', $cart->checkout_id)
                        ->where('user_id', $cart->user_id)
                        ->where('customer_id', $customer->id)
                        ->where('status', 'approved')
                        ->where('created_at', '>=', $cart->last_update)
                        ->first(); // se existe uma compra aprovada apos a data que o usuario estava no carrinho

                    if (!empty($order)) 
                        continue;
                }

                $cart->is_abandoned = 1;
                $cart->save();

                $user = User::find($cart->user_id);
                $checkout = Checkout::find($cart->checkout_id);
                $product = Product::find($checkout->product_id);

                /**
                 * Envia e-mail para o cliente com os dados de login
                 */
                $data = [
                    "name" => $cart->name,
                    "email" => $cart->email,
                    "phone" => $cart->phone,
                    "checkout" => $checkout,
                    "user" => $user,
                    "product" => $product,
                    "warranty_time" => $product->warranty_time,
                    "url" => $cart->url,
                ];

                Email::to($cart->email)
                    ->title("Carrinho abandonado")
                    ->subject("Os itens em seu carrinho estão esperando por você!")
                    ->body(Email::view('abandonedCart', $data))
                    ->send();
            }

        }
    }
}