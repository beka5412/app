<?php

namespace Backend\Controllers;

use Illuminate\Database\Capsule\Manager as DB;
use Backend\Entities\Abstracts\SellerBalance;
use Backend\Entities\Abstracts\Stripe\StripeWebhookQueue;
use Backend\Enums\EmailTemplate\EEmailTemplateType;
use Backend\Models\AppAstronmembersIntegration;
use Backend\Models\AppMemberkitIntegration;
use Backend\Models\Customer;
use Backend\Models\EmailMessage;
use Backend\Models\EmailQueue;
use Backend\Models\EmailTemplate;
use Backend\Models\Order;
use Backend\Models\Product;
use Backend\Services\RocketPanel\RocketPanel;
use Backend\Types\Astronmembers\AstronmembersQueueData;
use Backend\Types\Astronmembers\AstronmembersType;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\SellerCredit\SellerCreditType;
use Backend\Types\Stripe\StripeWebhookType;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Backend\Notifiers\Email\Mailer as Email;
use Backend\Http\Request;
use Backend\Services\{
    IPag\IPag,
    PagarMe\PagarMe
};
use Backend\App;
use Backend\Template\View;
use Backend\Http\Response;
use Backend\Attributes\Route;
use Backend\Entities\Abstracts\Astronmembers\AstronmembersQueue;
use Backend\Entities\Abstracts\Iugu\IuguChargeQueue;
use Backend\Entities\Abstracts\Memberkit\MemberkitQueue;
use Backend\Entities\Abstracts\SellerCreditQueue;
use Backend\Entities\Abstracts\Sellflux\SellfluxQueue;
use Backend\Entities\Abstracts\Utmify\UtmifyQueue;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Enums\Order\EOrderStatus;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Models\AppSellfluxIntegration;
use Backend\Models\Balance;
use Backend\Models\Invoice;
use Backend\Models\UtmifyQueue as ModelsUtmifyQueue;
use Backend\Models\IuguChargeQueue as ModelsIuguChargeQueue;
use Backend\Models\Smtp as ModelsSmtp;
use Backend\Models\Subscription;
use Backend\Services\Cademi\CademiRest;
use Backend\Services\GetNet\GetNet;
use Backend\Types\SellerCredit\ESellerCreditQueueStatus;
use Backend\Types\SellerCredit\SellerCreditBodyWhere;
use Backend\Types\SellerCredit\SellerQueueUpdateData;
use Backend\Types\SellerCredit\SellerQueueUpdateWhere;
use Backend\Types\Utmify\UtmifyType;
use Backend\Services\OneSignal\OneSignal;
use Backend\Types\Iugu\IuguChargeQueueDataList;
use Backend\Types\Iugu\IuguChargeType;
use Backend\Types\Memberkit\MemberkitQueueData;
use Backend\Types\Memberkit\MemberkitType;
use Backend\Types\Sellflux\SellfluxQueueData;
use Backend\Types\Sellflux\SellfluxType;

class Test
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function date()
    {
        echo today();
    }

    public function push_email()
    {
        $data = [
            "platform" => "LotuzPay",
            "username" => "Fulano",
            "image" => "https://app.lotuzpay.com/images/logo-dark.png",
            "product_name" => "Arroz",
            "total" => 25.59,
            "symbol" => currency_code_to_symbol('usd'),
            "email" => "quielbala@gmail.com",
            "password" => "12345678",
            "login_url" => get_subdomain_serialized('purchase') . "/login/token/ABC123",
            "product_author" => "Ezequiel",
            "product_support_email" => "quielbala@gmail.com",
            "product_warranty" => 7,
            "transaction_id" => "123123123123"
        ];

        send_email("quielbala@gmail.com", $data, EEmailTemplatePath::PURCHASE_APPROVED, 'fr_FR');
        // send_email("quielbala@gmail.com", $data, EEmailTemplateType::PURCHASE_APPROVED, 'es_MX');
    }


    public function email()
    {

        // $email = Email::to('quielbala@gmail.com')
        //     ->title('Açentós | Caminhão | Maçã')
        //     ->subject('Açuntô')
        //     ->body('
        //     <h1> Ola </h1>
        //     <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAR0AAAEdCAIAAAC+CCQsAAAABnRSTlMA/wD/AP83WBt9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAIN0lEQVR4nO3dwW7EuhFFwTjI///yy14IBBA8TcpO1dYeSTP2BcEesvnzzz///AtI/fv2A8AfJFfQkyvoyRX05Ap6cgU9uYKeXEFPrqAnV9CTK+jJFfTkCnpyBT25gp5cQU+uoCdX0JMr6P1n58U/Pz/Vc7x7NOF4v+97x46dZ77VC2Tp/T5+eemjW7Jz5aW/0dxbeLfz5zZeQU+uoCdX0JMr6G3VLR7Caf3OTP191rs0y196qveH3PHN3qlLH937a3cc+69bYryCnlxBT66gJ1fQK+sWD3Oz/Lliw9KVdxYNvP/ykp2Je7iQ5WGn8rTjVm3pwXgFPbmCnlxBT66gN1i3+IhjSyjeLxXubdmppiz5A0sobjFeQU+uoCdX0JMr6P2FukW4WOFY+4ed+x5bnLFTa5lbyfErGK+gJ1fQkyvoyRX0BusWc5PRnR4VO1desvNUH9mREdYe5v5kS49xjPEKenIFPbmCnlxBr6xbHDvoYWntwq/46budfSJ//v0e+69bYryCnlxBT66gJ1fQ+/nI99M75ibQOz6ym2PpyrcOXP0D/4QPxivoyRX05Ap6cgW9rbrF3NR8Z058bGr+LpzWzznW7uLY0o1wI8xONIxX0JMr6MkV9OQKeuV6i1tNLd8f49Y091jN4yO1hx23zoyde0fGK+jJFfTkCnpyBb2t/hZzh0TcmiLf2t0wd9Jp2Fp06adLFYK5Vqq3lrkYr6AnV9CTK+jJFfS26ha/Yg3B3Akac5PgsHxyazXG3FEsv6IZhvEKenIFPbmCnlxBr1xvMVds2Jma3+rLufMYO6sEwvpQWFs6tu7hI6t8jFfQkyvoyRX05Ap61/pb/Iq1GkvCfqBLr313bB6/s21k6ZfnKk/qFvBpcgU9uYKeXEHv2nkiD2HLy1sHkIYPOddLdOlStz72W3WakPEKenIFPbmCnlxBb2ufyMPcd+rHzsk89hgPO10sj1WPwivv/HLYLfT9tdZbwLfIFfTkCnpyBb3Bvpzvk+C5CsGxThJLlwqXUMwdEbLj2MKdW1WNJcYr6MkV9OQKenIFvXK9RXgMxLFiwzeFR4Qs3ejYtpGlFSS31mpYbwHfIlfQkyvoyRX0PnqeyEPYHGLpMcKDPB7mDlzdmW3PNYf4A3uIlhivoCdX0JMr6MkV9M715Zw72OLWOSZLvxxeasmx+4aFqLm/0THGK+jJFfTkCnpyBb1yn8iSY40WdvpqPMyt8zh2dmu4l+dhqSJyrEXHXCvVd8Yr6MkV9OQKenIFvbIvZzgLDFsa7Nw3XKxw66iO8PyUpfu++/MdSoxX0JMr6MkV9OQKelv7RJ7X6nZzLAm3byzd6JudFUK39uOEju3HeTBeQU+uoCdX0JMr6JV9Od9/OtfS4NbahVtLKB7mdq/cahwyt4nmWDMM4xX05Ap6cgU9uYJeuU/k3dzZHA/hyoa5/pg7rz3WpvNY+eTWH2XuysYr6MkV9OQKenIFvWt9Od8dK1QcWzNxrJPER0oRSz7SV8M+Efg0uYKeXEFPrqBX9rd4XvrSroq5CsHcsaihuU9jbkPKR6hbwKfJFfTkCnpyBb3BusXzTqdOpNyZix/7Pj5sHXGrArQkbFkx91r7RODT5Ap6cgU9uYLeYF/O94NAwyuHzTDmVlTMPdWxc0yONbWce6qlT3KH8Qp6cgU9uYKeXEHvK/tE5r4I/xWvPVYvudVp9NaNdhajWG8B3yJX0JMr6MkV9Mq+nEtHUC5damfyPXfGZlgwCDtvhsLP+f3K7+YWsuw81TvjFfTkCnpyBT25gt7geSJLc8Swi+WxPSZzdZqHucUKO9WUufLJsUYazhOB30SuoCdX0JMr6G3tE/nIVoiHuWNRv7nZIXy/7z6ya+bYibI7jFfQkyvoyRX05Ap6ZX+LuYMtdm40d6m5tRrhaaW3pvW3alpLv2y9BfwmcgU9uYKeXEHv3Dmozxv/hhUVYXXh3a1uknNvcO6Uk1vFlSXGK+jJFfTkCnpyBb3Bvpxh28qdJg3vjTTeL7Vj7piPnYcMP6uH8A/6bq6mFTJeQU+uoCdX0JMr6A32t5ibuT4cu/JD+AaP7fW4dabokmNLRua2jRivoCdX0JMr6MkV9L6y3mLnykuOfR9/rIozd2TGsVUgO/fVlxP+X8gV9OQKenIFvcG+nOG35u/mZq63VieEu0hu9ah4d+tScw0tHoxX0JMr6MkV9OQKetfOE5k7U/TW0RXvfsWumbl6yY6PnL+6xHgFPbmCnlxBT66gV/a3eHdrh8JH9mu8+/M9OeYeI+Q8Efg0uYKeXEFPrqC31d9ibpn93Kx36ZlvHZp6bIvNu6XTRuaWMoQVL/tE4BeTK+jJFfTkCnpbdYtbB0yEp6S+3+jYNPfdsbakx8wd5fruWBNP4xX05Ap6cgU9uYJeeZ7IrVYZczdaKmPsrE6Ym0DPrT5ZutHSlT9yJsgO4xX05Ap6cgU9uYJeWbd4ONamM5zIfmROfGvrR1iZCFtWHFucob8FfJpcQU+uoCdX0BusW8z55jkXD3M1gJ1L7eypuVVb+kjXziXGK+jJFfTkCnpyBb1fWbd4uFXGCA/jeFiqLiz99N1OH9KwNchc79R3zkGFT5Mr6MkV9OQKeoN1i2N7LsL2D3MT94ePXOqbyyB2+ojcWnzzYLyCnlxBT66gJ1fQ+5nr+Ri6VU542Lnyu7kawNyikHfhqbDvr11y7EbGK+jJFfTkCnpyBb2tugXwPxmvoCdX0JMr6MkV9OQKenIFPbmCnlxBT66gJ1fQkyvoyRX05Ap6cgU9uYKeXEFPrqAnV9CTK+j9F7me10GJcFdIAAAAAElFTkSuQmCC">
        //     ')
        //     ->debug(true)
        //     ->send('1');

        // $order = Order::find(1138);
        // $customer = Customer::find($order->customer_id);
        // $product = $order->product();

        // $email = Email::to($customer->email)
        //     ->title('Compra aprovada')
        //     ->subject("Você comprou $product->name")
        //     ->body(
        //         Email::view(
        //             'stripe/approvedPurchaseCustomer',
        //             compact('customer', 'product', 'order' /* , 'purchases' */)
        //         )
        //     )
        //     ->send();
        // print_r($email);

        $compact = [
            'name' => 'Ezequiel',
            'locale' => 'fr_FR'
        ];

        ['title' => $title, 'subject' => $subject, 'content' => $body] = get_object_vars(
            Email::readTemplate(
                Email::view('stripe/customer/approvedPurchase', $compact)
            )
        );

        $data = [
            "platform" => "LotuzPay",
            "username" => "User test",
            "image" => "https://app.lotuzpay.com/images/logo-dark.png",
            "product_name" => "Arroz",
            "total" => 25.59,
            "symbol" => currency_code_to_symbol('usd')->value,
            "email" => "quielbala@gmail.com",
            "password" => "12345678",
            "login_url" => get_subdomain_serialized('purchase') . "/login/token/ABC123",
            "product_author" => "Ezequiel",
            "product_support_email" => "quielbala@gmail.com",
            "product_warranty" => 7,
            "transaction_id" => "123123123123"
        ];

        $title = Email::template($title, $data);
        $subject = Email::template($subject, $data);
        $body = Email::template($body, $data);

        Email::to("quielbala@gmail.com")
            ->title($title)
            ->subject($subject)
            ->body($body)
            ->debug(1)
            ->send(1);
    }

    public function send_email_test()
    {
        Email::to("quielbala@gmail.com")
            ->title('Título teste')
            ->subject('Assunto teste')
            ->body('<h1>Corpo da mensagem</h1>')
            ->debug(1)
            ->send(1);
    }

    public function index()
    {
        $link = urlencode('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAR0AAAEdCAIAAAC+CCQsAAAABnRSTlMA/wD/AP83WBt9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAIN0lEQVR4nO3dwW7EuhFFwTjI///yy14IBBA8TcpO1dYeSTP2BcEesvnzzz///AtI/fv2A8AfJFfQkyvoyRX05Ap6cgU9uYKeXEFPrqAnV9CTK+jJFfTkCnpyBT25gp5cQU+uoCdX0JMr6P1n58U/Pz/Vc7x7NOF4v+97x46dZ77VC2Tp/T5+eemjW7Jz5aW/0dxbeLfz5zZeQU+uoCdX0JMr6G3VLR7Caf3OTP191rs0y196qveH3PHN3qlLH937a3cc+69bYryCnlxBT66gJ1fQK+sWD3Oz/Lliw9KVdxYNvP/ykp2Je7iQ5WGn8rTjVm3pwXgFPbmCnlxBT66gN1i3+IhjSyjeLxXubdmppiz5A0sobjFeQU+uoCdX0JMr6P2FukW4WOFY+4ed+x5bnLFTa5lbyfErGK+gJ1fQkyvoyRX0BusWc5PRnR4VO1desvNUH9mREdYe5v5kS49xjPEKenIFPbmCnlxBr6xbHDvoYWntwq/46budfSJ//v0e+69bYryCnlxBT66gJ1fQ+/nI99M75ibQOz6ym2PpyrcOXP0D/4QPxivoyRX05Ap6cgW9rbrF3NR8Z058bGr+LpzWzznW7uLY0o1wI8xONIxX0JMr6MkV9OQKeuV6i1tNLd8f49Y091jN4yO1hx23zoyde0fGK+jJFfTkCnpyBb2t/hZzh0TcmiLf2t0wd9Jp2Fp06adLFYK5Vqq3lrkYr6AnV9CTK+jJFfS26ha/Yg3B3Akac5PgsHxyazXG3FEsv6IZhvEKenIFPbmCnlxBr1xvMVds2Jma3+rLufMYO6sEwvpQWFs6tu7hI6t8jFfQkyvoyRX05Ap61/pb/Iq1GkvCfqBLr313bB6/s21k6ZfnKk/qFvBpcgU9uYKeXEHv2nkiD2HLy1sHkIYPOddLdOlStz72W3WakPEKenIFPbmCnlxBb2ufyMPcd+rHzsk89hgPO10sj1WPwivv/HLYLfT9tdZbwLfIFfTkCnpyBb3Bvpzvk+C5CsGxThJLlwqXUMwdEbLj2MKdW1WNJcYr6MkV9OQKenIFvXK9RXgMxLFiwzeFR4Qs3ejYtpGlFSS31mpYbwHfIlfQkyvoyRX0PnqeyEPYHGLpMcKDPB7mDlzdmW3PNYf4A3uIlhivoCdX0JMr6MkV9M715Zw72OLWOSZLvxxeasmx+4aFqLm/0THGK+jJFfTkCnpyBb1yn8iSY40WdvpqPMyt8zh2dmu4l+dhqSJyrEXHXCvVd8Yr6MkV9OQKenIFvbIvZzgLDFsa7Nw3XKxw66iO8PyUpfu++/MdSoxX0JMr6MkV9OQKelv7RJ7X6nZzLAm3byzd6JudFUK39uOEju3HeTBeQU+uoCdX0JMr6JV9Od9/OtfS4NbahVtLKB7mdq/cahwyt4nmWDMM4xX05Ap6cgU9uYJeuU/k3dzZHA/hyoa5/pg7rz3WpvNY+eTWH2XuysYr6MkV9OQKenIFvWt9Od8dK1QcWzNxrJPER0oRSz7SV8M+Efg0uYKeXEFPrqBX9rd4XvrSroq5CsHcsaihuU9jbkPKR6hbwKfJFfTkCnpyBb3BusXzTqdOpNyZix/7Pj5sHXGrArQkbFkx91r7RODT5Ap6cgU9uYLeYF/O94NAwyuHzTDmVlTMPdWxc0yONbWce6qlT3KH8Qp6cgU9uYKeXEHvK/tE5r4I/xWvPVYvudVp9NaNdhajWG8B3yJX0JMr6MkV9Mq+nEtHUC5damfyPXfGZlgwCDtvhsLP+f3K7+YWsuw81TvjFfTkCnpyBT25gt7geSJLc8Swi+WxPSZzdZqHucUKO9WUufLJsUYazhOB30SuoCdX0JMr6G3tE/nIVoiHuWNRv7nZIXy/7z6ya+bYibI7jFfQkyvoyRX05Ap6ZX+LuYMtdm40d6m5tRrhaaW3pvW3alpLv2y9BfwmcgU9uYKeXEHv3Dmozxv/hhUVYXXh3a1uknNvcO6Uk1vFlSXGK+jJFfTkCnpyBb3Bvpxh28qdJg3vjTTeL7Vj7piPnYcMP6uH8A/6bq6mFTJeQU+uoCdX0JMr6A32t5ibuT4cu/JD+AaP7fW4dabokmNLRua2jRivoCdX0JMr6MkV9L6y3mLnykuOfR9/rIozd2TGsVUgO/fVlxP+X8gV9OQKenIFvcG+nOG35u/mZq63VieEu0hu9ah4d+tScw0tHoxX0JMr6MkV9OQKetfOE5k7U/TW0RXvfsWumbl6yY6PnL+6xHgFPbmCnlxBT66gV/a3eHdrh8JH9mu8+/M9OeYeI+Q8Efg0uYKeXEFPrqC31d9ibpn93Kx36ZlvHZp6bIvNu6XTRuaWMoQVL/tE4BeTK+jJFfTkCnpbdYtbB0yEp6S+3+jYNPfdsbakx8wd5fruWBNP4xX05Ap6cgU9uYJeeZ7IrVYZczdaKmPsrE6Ym0DPrT5ZutHSlT9yJsgO4xX05Ap6cgU9uYJeWbd4ONamM5zIfmROfGvrR1iZCFtWHFucob8FfJpcQU+uoCdX0BusW8z55jkXD3M1gJ1L7eypuVVb+kjXziXGK+jJFfTkCnpyBb1fWbd4uFXGCA/jeFiqLiz99N1OH9KwNchc79R3zkGFT5Mr6MkV9OQKeoN1i2N7LsL2D3MT94ePXOqbyyB2+ojcWnzzYLyCnlxBT66gJ1fQ+5nr+Ri6VU542Lnyu7kawNyikHfhqbDvr11y7EbGK+jJFfTkCnpyBb2tugXwPxmvoCdX0JMr6MkV9OQKenIFPbmCnlxBT66gJ1fQkyvoyRX05Ap6cgU9uYKeXEFPrqAnV9CTK+j9F7me10GJcFdIAAAAAElFTkSuQmCC');
        $href = site_url() . "/image/base64?data=$link";
        echo "<a href='$href'>$href</a>";
        // $email = Email::to('quielbala@gmail.com')
        //     ->title('Açentós | Caminhão | Maçã')
        //     ->subject('Açuntô')
        //     // ->body(Email::view('newUser', ['customer' => (Object) ['name' => "Ezequiel", "email" => "ezektrader@gmail.com", "password" => '123456789']]))
        //     ->body('
        //     <h1> Ola </h1>
        //     <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAR0AAAEdCAIAAAC+CCQsAAAABnRSTlMA/wD/AP83WBt9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAIN0lEQVR4nO3dwW7EuhFFwTjI///yy14IBBA8TcpO1dYeSTP2BcEesvnzzz///AtI/fv2A8AfJFfQkyvoyRX05Ap6cgU9uYKeXEFPrqAnV9CTK+jJFfTkCnpyBT25gp5cQU+uoCdX0JMr6P1n58U/Pz/Vc7x7NOF4v+97x46dZ77VC2Tp/T5+eemjW7Jz5aW/0dxbeLfz5zZeQU+uoCdX0JMr6G3VLR7Caf3OTP191rs0y196qveH3PHN3qlLH937a3cc+69bYryCnlxBT66gJ1fQK+sWD3Oz/Lliw9KVdxYNvP/ykp2Je7iQ5WGn8rTjVm3pwXgFPbmCnlxBT66gN1i3+IhjSyjeLxXubdmppiz5A0sobjFeQU+uoCdX0JMr6P2FukW4WOFY+4ed+x5bnLFTa5lbyfErGK+gJ1fQkyvoyRX0BusWc5PRnR4VO1desvNUH9mREdYe5v5kS49xjPEKenIFPbmCnlxBr6xbHDvoYWntwq/46budfSJ//v0e+69bYryCnlxBT66gJ1fQ+/nI99M75ibQOz6ym2PpyrcOXP0D/4QPxivoyRX05Ap6cgW9rbrF3NR8Z058bGr+LpzWzznW7uLY0o1wI8xONIxX0JMr6MkV9OQKeuV6i1tNLd8f49Y091jN4yO1hx23zoyde0fGK+jJFfTkCnpyBb2t/hZzh0TcmiLf2t0wd9Jp2Fp06adLFYK5Vqq3lrkYr6AnV9CTK+jJFfS26ha/Yg3B3Akac5PgsHxyazXG3FEsv6IZhvEKenIFPbmCnlxBr1xvMVds2Jma3+rLufMYO6sEwvpQWFs6tu7hI6t8jFfQkyvoyRX05Ap61/pb/Iq1GkvCfqBLr313bB6/s21k6ZfnKk/qFvBpcgU9uYKeXEHv2nkiD2HLy1sHkIYPOddLdOlStz72W3WakPEKenIFPbmCnlxBb2ufyMPcd+rHzsk89hgPO10sj1WPwivv/HLYLfT9tdZbwLfIFfTkCnpyBb3Bvpzvk+C5CsGxThJLlwqXUMwdEbLj2MKdW1WNJcYr6MkV9OQKenIFvXK9RXgMxLFiwzeFR4Qs3ejYtpGlFSS31mpYbwHfIlfQkyvoyRX0PnqeyEPYHGLpMcKDPB7mDlzdmW3PNYf4A3uIlhivoCdX0JMr6MkV9M715Zw72OLWOSZLvxxeasmx+4aFqLm/0THGK+jJFfTkCnpyBb1yn8iSY40WdvpqPMyt8zh2dmu4l+dhqSJyrEXHXCvVd8Yr6MkV9OQKenIFvbIvZzgLDFsa7Nw3XKxw66iO8PyUpfu++/MdSoxX0JMr6MkV9OQKelv7RJ7X6nZzLAm3byzd6JudFUK39uOEju3HeTBeQU+uoCdX0JMr6JV9Od9/OtfS4NbahVtLKB7mdq/cahwyt4nmWDMM4xX05Ap6cgU9uYJeuU/k3dzZHA/hyoa5/pg7rz3WpvNY+eTWH2XuysYr6MkV9OQKenIFvWt9Od8dK1QcWzNxrJPER0oRSz7SV8M+Efg0uYKeXEFPrqBX9rd4XvrSroq5CsHcsaihuU9jbkPKR6hbwKfJFfTkCnpyBb3BusXzTqdOpNyZix/7Pj5sHXGrArQkbFkx91r7RODT5Ap6cgU9uYLeYF/O94NAwyuHzTDmVlTMPdWxc0yONbWce6qlT3KH8Qp6cgU9uYKeXEHvK/tE5r4I/xWvPVYvudVp9NaNdhajWG8B3yJX0JMr6MkV9Mq+nEtHUC5damfyPXfGZlgwCDtvhsLP+f3K7+YWsuw81TvjFfTkCnpyBT25gt7geSJLc8Swi+WxPSZzdZqHucUKO9WUufLJsUYazhOB30SuoCdX0JMr6G3tE/nIVoiHuWNRv7nZIXy/7z6ya+bYibI7jFfQkyvoyRX05Ap6ZX+LuYMtdm40d6m5tRrhaaW3pvW3alpLv2y9BfwmcgU9uYKeXEHv3Dmozxv/hhUVYXXh3a1uknNvcO6Uk1vFlSXGK+jJFfTkCnpyBb3Bvpxh28qdJg3vjTTeL7Vj7piPnYcMP6uH8A/6bq6mFTJeQU+uoCdX0JMr6A32t5ibuT4cu/JD+AaP7fW4dabokmNLRua2jRivoCdX0JMr6MkV9L6y3mLnykuOfR9/rIozd2TGsVUgO/fVlxP+X8gV9OQKenIFvcG+nOG35u/mZq63VieEu0hu9ah4d+tScw0tHoxX0JMr6MkV9OQKetfOE5k7U/TW0RXvfsWumbl6yY6PnL+6xHgFPbmCnlxBT66gV/a3eHdrh8JH9mu8+/M9OeYeI+Q8Efg0uYKeXEFPrqC31d9ibpn93Kx36ZlvHZp6bIvNu6XTRuaWMoQVL/tE4BeTK+jJFfTkCnpbdYtbB0yEp6S+3+jYNPfdsbakx8wd5fruWBNP4xX05Ap6cgU9uYJeeZ7IrVYZczdaKmPsrE6Ym0DPrT5ZutHSlT9yJsgO4xX05Ap6cgU9uYJeWbd4ONamM5zIfmROfGvrR1iZCFtWHFucob8FfJpcQU+uoCdX0BusW8z55jkXD3M1gJ1L7eypuVVb+kjXziXGK+jJFfTkCnpyBb1fWbd4uFXGCA/jeFiqLiz99N1OH9KwNchc79R3zkGFT5Mr6MkV9OQKeoN1i2N7LsL2D3MT94ePXOqbyyB2+ojcWnzzYLyCnlxBT66gJ1fQ+5nr+Ri6VU542Lnyu7kawNyikHfhqbDvr11y7EbGK+jJFfTkCnpyBb2tugXwPxmvoCdX0JMr6MkV9OQKenIFPbmCnlxBT66gJ1fQkyvoyRX05Ap6cgU9uYKeXEFPrqAnV9CTK+j9F7me10GJcFdIAAAAAElFTkSuQmCC">
        //     ')
        //     ->send();
    }

    public function send()
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try
        {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = env('STMP_HOST');                  //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = env('STMP_USERNAME');  //SMTP username
            $mail->Password   = env('STMP_PASSWORD');                     //SMTP password
            $mail->Port       = env('STMP_PORT');                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->SMTPSecure = $mail->Port == 587 ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;

            //Recipients
            $mail->setFrom(env('SMTP_FROM'), ('Título Avião €'));
            $mail->addAddress('quielbala@gmail.com');
            // $mail->addAddress('contato@rocketleads.com.br');

            //Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';                           //Set email format to HTML
            $mail->Subject = ('Avião €');
            $mail->Body    = ('Isso é  uma mensagem HTML <b>em negrito! €</b>');
            $mail->AltBody = strip_tags($mail->Body);

            $mail->send();
            echo 'Message has been sent';
        }
        catch (Exception $e)
        {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function pagarme(Request $request)
    {
        header("Content-Type: application/json");
        $pagarme = new PagarMe;
        // echo $pagarme->pix([]);
        echo $pagarme->billet([]);
    }

    public function hash_make(Request $request, $password)
    {
        echo hash_make($password);
    }

    public function button_upsell(Request $request)
    {
        $title = 'Button Upsell';
        $context = 'public';
        View::render('frontend/view/test/upsell/indexView.php', compact('title', 'context'));
        echo "<script src=\"" . site_url() . "/upsell.min.js?v=" . uniqid() . "\"></script>";
    }

    public function js_redirect()
    {
        Response::jsRedirect('http://google.com');
    }
    public function html_redirect()
    {
        Response::htmlRedirect('http://google.com');
    }

    public function ipag_get_cards()
    {
        $cards = [
            '5f5dfa9b-f345-4fcb-b406-06645cc43c7c',
            'bbba0c33-6398-4580-b1f9-7ebedd63046a',
            '3b455bde-ce24-4dd1-b078-a5673b65d752'
        ];
        $ipag = new Ipag;
        $result = array_map(
            fn ($card) => json_decode($card),
            $ipag->getCards($cards)
        );
        print_r($result);
    }

    public function ipag_consult_subscription($request, $id)
    {
        $ipag = new IPag;
        $result = $ipag->consultSubscription($id);
        debug_html($result);
    }

    public function getnet($request)
    {
        $getnet = new GetNet;
        $access_token = $getnet->accessToken();
        $object = (array) json_decode($getnet->tokenizeCard($access_token, [
            'card_number' => '5155901222280001',
            'customer_id' => 'customer_21081826'
        ])); // $number_token
        extract($object);
        print_r($number_token);
    }

    public function panel()
    {
        $res = RocketPanel::payload([
            'user' => [
                "email" => "ezequiel.teste-9@gmail.com",
                "name" => "Ezequiel Moraes",
                "password" => "123456789"
            ],
            "expires_at" => date("Y-m-d H:i:s", strtotime(today() . " + 1 year")),
            'platforms' => [17, 16, 15], // rocketplanner, rocketbots, rocketlink 
        ])->send();

        echo '<pre>';
        print_r($res);
    }

    public function upsell_x(Request $request, $product_id)
    {
        $product = Product::findOrFail($product_id);
?>
        <style>
            body {
                background-color: #666;
            }
        </style>

        <!-- <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
        <link href="<?= site_url() ?>/snippets/upsell.min.css" rel="stylesheet">

        <div class="upsell-wrapper">
            <button id="btnUpsell" class="button" data-product-id="<?= $product_id ?>">
                Comprar <?= $product->name ?>
            </button>
            <div style="text-align: center">
                <div class="error-payment" id="elementErrorPayment"></div>
            </div>
        </div>

        <script src="<?= site_url() ?>/snippets/upsell.min.js"></script> -->


        <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
        <link href="<?= site_url() ?>/snippets/upsell.min.css" rel="stylesheet">

        <div class="upsell-wrapper">
            <div class="lotuzpay-flex lotuzpay-gap">

                <button id="btnUpsell" class="lotuzpay-button" data-product-id="<?= $product_id ?>">
                    Comprar
                </button>


                <a href="<?= site_url() ?>/test/upsell-x/253" id="btnUpsellReject" class="lotuzpay-button-reject">
                    Não quero
                </a>

            </div>

            <div style="text-align: center">
                <div class="error-payment" id="elementErrorPayment"></div>
            </div>
        </div>

        <script src="<?= site_url() ?>/snippets/upsell.min.js"></script>

    <?php }

    public function upsell_1()
    { ?>
        <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
        <link href="<?= site_url() ?>/snippets/upsell.min.css" rel="stylesheet">

        <div class="upsell-wrapper">
            <button id="btnUpsell" class="button" data-product-id="241">
                Comprar 1
            </button>
            <div style="text-align: center">
                <div class="success-payment" id="elementSuccessPayment"></div>
                <div class="error-payment" id="elementErrorPayment"></div>
            </div>
        </div>

        <script src="<?= site_url() ?>/snippets/upsell.min.js"></script>
    <?php }

    public function upsell_2(Request $request)
    { ?>
        <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
        <link href="<?= site_url() ?>/snippets/upsell.min.css" rel="stylesheet">

        <div class="upsell-wrapper">
            <button id="btnUpsell" class="button" data-product-id="242">
                Comprar 2
            </button>
            <div style="text-align: center">
                <div class="success-payment" id="elementSuccessPayment"></div>
                <div class="error-payment" id="elementErrorPayment"></div>
            </div>
        </div>

        <script src="<?= site_url() ?>/snippets/upsell.min.js"></script>
        <?php }

    public function transfer(Request $request)
    {

        $charges = <<<EOF
{
    "object": "list",
    "data": [
        {
            "id": "ch_3PZvunE2rDcb2TfP1oZjIKgQ",
            "object": "charge",
            "amount": 3500,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "77498",
                    "state": null
                },
                "email": "patriciadelatorre81@yahoo.com",
                "name": "Patricia Aguirre",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720361889,
            "currency": "usd",
            "customer": "cus_QQnMm21Y6C4CCU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "e054cc72-1b8f-49a2-b980-2e4a664e00a5"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZvunE2rDcb2TfP1xte0juH",
            "payment_method": "pm_1PZvtvE2rDcb2TfPD3vmqv34",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 11,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "53RilQDNd0DkSjdk",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0681",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 3500,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZvuPE2rDcb2TfP0rjNxGJ1",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 2990,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZvuPE2rDcb2TfP0xdYhrTZ",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "77498",
                    "state": null
                },
                "email": "patriciadelatorre81@yahoo.com",
                "name": "Patricia Aguirre",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720361865,
            "currency": "usd",
            "customer": "cus_QQnMm21Y6C4CCU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "9170b8b2-fbdd-4a5d-8e4e-e6405819472e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": "elevated_risk_level",
                "risk_level": "elevated",
                "rule": "manual_review_if_elevated_risk",
                "seller_message": "Stripe evaluated this payment as having elevated risk, and placed it in your manual review queue.",
                "type": "manual_review"
            },
            "paid": true,
            "payment_intent": "pi_3PZvuPE2rDcb2TfP0zDi4P8Z",
            "payment_method": "pm_1PZvtvE2rDcb2TfPD3vmqv34",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2990,
                    "authorization_code": "009255",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 11,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "53RilQDNd0DkSjdk",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0681",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBjqiPJC5qzosFo1LXGdX-sjmrxGnHS8dU4epVk0Or_OEWhpdWdw2TUd_9FIoqvK2snIgr4M",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZvmDE2rDcb2TfP1eKhBAl5",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZvmDE2rDcb2TfP14MIc75P",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "77498",
                    "state": null
                },
                "email": "patriciadelatorre81@yahoo.com",
                "name": "Patricia Aguirre",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720361835,
            "currency": "usd",
            "customer": "cus_QQnMm21Y6C4CCU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "8bbe2e1f-15a8-456e-bd74-a384013da032"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZvmDE2rDcb2TfP1gSp24px",
            "payment_method": "pm_1PZvtvE2rDcb2TfPD3vmqv34",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "007022",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 11,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "53RilQDNd0DkSjdk",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0681",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBpGHEYL06josFhrM1JZ_XBFAPNEr8WCmBwHZ2Yi0t9-HEB7DM5LRxmupH0bhnd75o6ipyPE",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZvnaE2rDcb2TfP18Kxh4Xv",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "titalopezp@hotmail.com",
                "name": "MARTHA LETICIA LÓPEZ PÉREZ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720361442,
            "currency": "usd",
            "customer": "cus_QQnLIUzFZmBBoJ",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "32efc405-5389-40c1-9add-cf6851d7b021"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZvnaE2rDcb2TfP1djVtXGz",
            "payment_method": "pm_1PZvnDE2rDcb2TfPjEZkUMck",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 2,
                    "exp_year": 2032,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "QrXcpcUSCOWE264F",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6161",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZvl2E2rDcb2TfP0Re5RmKI",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 2700,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZvl2E2rDcb2TfP0RNwEqm0",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "titalopezp@hotmail.com",
                "name": "MARTHA LETICIA LÓPEZ PÉREZ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720361419,
            "currency": "usd",
            "customer": "cus_QQnLIUzFZmBBoJ",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "e65ae99c-19bb-46ab-a564-502a012704ff"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": "elevated_risk_level",
                "risk_level": "elevated",
                "rule": "manual_review_if_elevated_risk",
                "seller_message": "Stripe evaluated this payment as having elevated risk, and placed it in your manual review queue.",
                "type": "manual_review"
            },
            "paid": true,
            "payment_intent": "pi_3PZvl2E2rDcb2TfP02JRZQMA",
            "payment_method": "pm_1PZvnDE2rDcb2TfPjEZkUMck",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "423215",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 2,
                    "exp_year": 2032,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "QrXcpcUSCOWE264F",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6161",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBjHTlzVWKTosFkPGKgOYu7nUT7Awj3Y-vMqzHoUKmrugMyJkoGkd4K9SAYTWGQzdxJe8hWw",
            "refunded": true,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZvlcE2rDcb2TfP1ab9OWy3",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "vicalrm@gmail.com",
                "name": "Victor A Ramirez Meraz ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720361320,
            "currency": "usd",
            "customer": "cus_QQnGFKrF7KWvkb",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "bcb21bed-ea54-4841-a0fc-1881dfcfa52a"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZvlcE2rDcb2TfP14ls1ncz",
            "payment_method": "pm_1PZvl7E2rDcb2TfPx788gyVg",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 8,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "T1l0bEql1Mldm2dq",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZvfVE2rDcb2TfP0OuPERLE",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZvfVE2rDcb2TfP0zGF4t0h",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "vicalrm@gmail.com",
                "name": "Victor A Ramirez Meraz ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720361290,
            "currency": "usd",
            "customer": "cus_QQnGFKrF7KWvkb",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "61b74411-0deb-4b70-855f-09508c825911"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": "elevated_risk_level",
                "risk_level": "elevated",
                "rule": "manual_review_if_elevated_risk",
                "seller_message": "Stripe evaluated this payment as having elevated risk, and placed it in your manual review queue.",
                "type": "manual_review"
            },
            "paid": true,
            "payment_intent": "pi_3PZvfVE2rDcb2TfP0Sj31MVS",
            "payment_method": "pm_1PZvl7E2rDcb2TfPx788gyVg",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "760556",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 8,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "T1l0bEql1Mldm2dq",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBgmwLPqMPDosFmYpc0jYF-9iWCM4Fi3FTTbO1hp9lNcD4VdLOvLyZhbNJvb2oNI4-JgYYYI",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZv8QE2rDcb2TfP0vaHZkZa",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "Ceytkaal@hotmail.com",
                "name": "Evangelio herrera",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720359582,
            "currency": "usd",
            "customer": "cus_QQmhHPy5441RlX",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card does not support this type of purchase.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "a8fce7ac-d36e-455a-8188-3c715886f85e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "transaction_not_allowed",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `transaction_not_allowed`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZv8QE2rDcb2TfP04REYge9",
            "payment_method": "pm_1PZvJZE2rDcb2TfPAPea7lTW",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 7,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "R1g9Awrxobq8sXOL",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "1730",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZv8QE2rDcb2TfP00YzSPdc",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "manferm2@gmail.com",
                "name": "Manuel Fernández montaño",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720359173,
            "currency": "usd",
            "customer": "cus_QQmhHPy5441RlX",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "a8fce7ac-d36e-455a-8188-3c715886f85e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZv8QE2rDcb2TfP04REYge9",
            "payment_method": "pm_1PZvCzE2rDcb2TfPYNMjtsgl",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "B1KGkwFGrmTAddYS",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3817",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZv8QE2rDcb2TfP0PZInCca",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "manferm2@gmail.com",
                "name": "Manuel Fernández",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720359119,
            "currency": "usd",
            "customer": "cus_QQmhHPy5441RlX",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "a8fce7ac-d36e-455a-8188-3c715886f85e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "do_not_honor",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `do_not_honor`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZv8QE2rDcb2TfP04REYge9",
            "payment_method": "pm_1PZvC7E2rDcb2TfPVqjAOKUx",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "B1KGkwFGrmTAddYS",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3817",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZurdE2rDcb2TfP0N7MnqTJ",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "luis.540906@gmail.com",
                "name": "Luis M. Arroyo Garza",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720357849,
            "currency": "usd",
            "customer": "cus_QQmMxG6IRRmh9w",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "b5fa4149-861d-4dcc-9ea1-a80339075809"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZurdE2rDcb2TfP0ppIoccr",
            "payment_method": "pm_1PZurBE2rDcb2TfPa2ay2nqB",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "22MCGRPbgR7zY3I5",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "2478",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZunVE2rDcb2TfP1ceae8nK",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZunVE2rDcb2TfP1XOA9RJD",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "luis.540906@gmail.com",
                "name": "Luis M. Arroyo Garza",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720357821,
            "currency": "usd",
            "customer": "cus_QQmMxG6IRRmh9w",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "96530c0a-e9fe-4d69-9ceb-37123f5fff0a"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZunVE2rDcb2TfP1fv88u82",
            "payment_method": "pm_1PZurBE2rDcb2TfPa2ay2nqB",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "265368",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "22MCGRPbgR7zY3I5",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "2478",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBre2MABCXjosFgYdd4SWBHX0O2fLNsKiV5gADouzAVELCeABDd9I_ERse6Fe-yiXbAhzhRk",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZujYE2rDcb2TfP0Kwwfew0",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "20147",
                    "state": null
                },
                "email": "emedina2407@hotmail.com",
                "name": "Emiliano Medina",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720357348,
            "currency": "usd",
            "customer": "cus_QQmDzD8fyVToAQ",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "5d00821d-e7b4-4c96-bb07-f68ca84f4d5c"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZujYE2rDcb2TfP0LzADpNh",
            "payment_method": "pm_1PZuj6E2rDcb2TfPHJ7vk8pn",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 2,
                    "exp_year": 2029,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "tpAcx06X0QVGGpGP",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5089",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZuf5E2rDcb2TfP1BAtZ6Mh",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZuf5E2rDcb2TfP1gdUDVhI",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "20147",
                    "state": null
                },
                "email": "emedina2407@hotmail.com",
                "name": "Emiliano Medina",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720357321,
            "currency": "usd",
            "customer": "cus_QQmDzD8fyVToAQ",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "a67a2fc0-3cd3-469f-a582-7faf2fa8824c"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZuf5E2rDcb2TfP1Nui5X2W",
            "payment_method": "pm_1PZuj6E2rDcb2TfPHJ7vk8pn",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "06058D",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 2,
                    "exp_year": 2029,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "tpAcx06X0QVGGpGP",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5089",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBiq5CuxJXDosFv59Y1huAetl4-tE7F13oXzcLOG2ydKTQSVO7D_XJRPaDPND-jb2EfyAgyo",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZsz9E2rDcb2TfP09IayiyX",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": "Pembroke Pines ",
                    "country": "US",
                    "line1": "1181 Nw 162nd Ave",
                    "line2": "",
                    "postal_code": "33028",
                    "state": "FL"
                },
                "email": "perlazam@bellsouth.net",
                "name": "Inês Martinez",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720350627,
            "currency": "usd",
            "customer": "cus_QQkRvdrnWPZnOI",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "50aac6be-6637-41f6-bea9-5eb015dcc0f1"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZsz9E2rDcb2TfP0AoYnOEa",
            "payment_method": "pm_1PZsynE2rDcb2TfPa4hVvV7T",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": "unavailable",
                        "address_postal_code_check": "unavailable",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 9,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "hB8mMHyIj0I4Tnh5",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7311",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "6893",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZsweE2rDcb2TfP0nCj0kFf",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZsweE2rDcb2TfP0IAoMkNw",
            "billing_details": {
                "address": {
                    "city": "Pembroke Pines ",
                    "country": "US",
                    "line1": "1181 Nw 162nd Ave",
                    "line2": "",
                    "postal_code": "33028",
                    "state": "FL"
                },
                "email": "perlazam@bellsouth.net",
                "name": "Inês Martinez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720350605,
            "currency": "usd",
            "customer": "cus_QQkRvdrnWPZnOI",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "b79d2603-7e96-4c40-a80d-720b64b940d2"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZsweE2rDcb2TfP0n1sIUoh",
            "payment_method": "pm_1PZsynE2rDcb2TfPa4hVvV7T",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "037099",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": "pass",
                        "address_postal_code_check": "pass",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 9,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "hB8mMHyIj0I4Tnh5",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7311",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "6893",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBi_e5SnGMTosFi92X_wtCK2Up8TrjG6yCe3gn2eLbTXzDe6zuz9JDC5fjIhvtZvo4pKngCs",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZslkE2rDcb2TfP0AOd5SzE",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "78503",
                    "state": null
                },
                "email": "marcovidales1@gmail.com",
                "name": "Marco Aurelio Vidales Diaz",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720349796,
            "currency": "usd",
            "customer": "cus_QQkCmPBkRzdUIF",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "84893a80-b62a-4735-bbb1-49d367a69628"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZslkE2rDcb2TfP0HF862S0",
            "payment_method": "pm_1PZslNE2rDcb2TfPc2V1XbG2",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "zCZCfByCT7wJT55K",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0916",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZshdE2rDcb2TfP0eDpScCa",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZshdE2rDcb2TfP0We0xbWU",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "78503",
                    "state": null
                },
                "email": "marcovidales1@gmail.com",
                "name": "Marco Aurelio Vidales Diaz",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720349773,
            "currency": "usd",
            "customer": "cus_QQkCmPBkRzdUIF",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "61562fe4-7a4d-45d4-8242-5ed83a8f7671"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZshdE2rDcb2TfP0xvvLCug",
            "payment_method": "pm_1PZslNE2rDcb2TfPc2V1XbG2",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "969478",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "zCZCfByCT7wJT55K",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0916",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBvkPIwa9GTosFuf5uaG6qjQ4vinV3DYUEiFkvMFTbByHcKduCB5pxV8H3TlJMbRu5a3_U8Q",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZsUuE2rDcb2TfP1LGQy5F2",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "64012",
                    "state": null
                },
                "email": "rafaeltorres0705@gmail.com",
                "name": "Rafael Torres",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": false,
            "created": 1720348752,
            "currency": "usd",
            "customer": "cus_QQjohlMfnkYpgt",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "5c659892-a9fd-4bdb-b6af-42281456cf15"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "do_not_honor",
                "risk_level": "elevated",
                "seller_message": "The bank returned the decline code `do_not_honor`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZsUuE2rDcb2TfP1iZ1zP35",
            "payment_method": "pm_1PZsU1E2rDcb2TfPGoT8XBzQ",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 3,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3TpRWzpKdFyYzNz3",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "2777",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZsKzE2rDcb2TfP0BoOxbNT",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZsKzE2rDcb2TfP0qm3bz10",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "64012",
                    "state": null
                },
                "email": "rafaeltorres0705@gmail.com",
                "name": "Rafael Torres",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720348713,
            "currency": "usd",
            "customer": "cus_QQjohlMfnkYpgt",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "c405af1b-0c92-4e69-9fd3-631582c6084b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZsKzE2rDcb2TfP0EBr5C4x",
            "payment_method": "pm_1PZsU1E2rDcb2TfPGoT8XBzQ",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "007045",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 3,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3TpRWzpKdFyYzNz3",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "2777",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBt17ME6lETosFhZJlaCxOatpc4hNcejU6MupD7zHmY67J0Tl5zpEZfWBTt-xVeW2obHWvl4",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZsKzE2rDcb2TfP0QZgbSey",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "64012",
                    "state": null
                },
                "email": "rafaeltorres0705@gmail.com",
                "name": "Rafael Torres",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720348483,
            "currency": "usd",
            "customer": "cus_QQjohlMfnkYpgt",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "c405af1b-0c92-4e69-9fd3-631582c6084b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZsKzE2rDcb2TfP0EBr5C4x",
            "payment_method": "pm_1PZsQCE2rDcb2TfPPA5kdymM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 10,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "TjlcXytdkTnk9syC",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4007",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZsKzE2rDcb2TfP0j0gMHZf",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "64012",
                    "state": null
                },
                "email": "rafaeltorres0705@gmail.com",
                "name": "Rafael Torres",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720348435,
            "currency": "usd",
            "customer": "cus_QQjohlMfnkYpgt",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "c405af1b-0c92-4e69-9fd3-631582c6084b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZsKzE2rDcb2TfP0EBr5C4x",
            "payment_method": "pm_1PZsPUE2rDcb2TfPfYJxDvz5",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 10,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "TjlcXytdkTnk9syC",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4007",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": {
                        "authentication_flow": "frictionless",
                        "electronic_commerce_indicator": "02",
                        "exemption_indicator": null,
                        "result": "authenticated",
                        "result_reason": null,
                        "transaction_id": "f0d3c7ae-4ad2-4b50-8be4-37401c3db651",
                        "version": "2.2.0"
                    },
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZs4LE2rDcb2TfP0F3YUhfr",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZs4LE2rDcb2TfP072SoJga",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "01104",
                    "state": null
                },
                "email": "secolla4591@gmail.com",
                "name": "Collazo Sonia",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720347311,
            "currency": "usd",
            "customer": "cus_QQjXrXovtaqIB2",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "1f67e1ab-74fb-43e8-8d6b-bb89466bfb99"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZs4LE2rDcb2TfP0CHxEgke",
            "payment_method": "pm_1PZs7eE2rDcb2TfPmFjr8i1a",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "111155",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 5,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "23VV6v3OkKdlNSzP",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7053",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBkFHx9D3FDosFiiAP_oS7r6SsL4BbuES56OF_IamhPO2FIV5rbxI_Gar3Xg50WHUAGG7CWA",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZroSE2rDcb2TfP0OhoVE8W",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "33033",
                    "state": null
                },
                "email": null,
                "name": null,
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": false,
            "created": 1720346120,
            "currency": "usd",
            "customer": "cus_QQjFxaQx1OUTcH",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "07e46bb2-fb2e-4b0c-8e2e-7b49be071d46"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "generic_decline",
                "risk_level": "elevated",
                "seller_message": "The bank did not return any further details with this decline.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZroSE2rDcb2TfP0UjEcNVb",
            "payment_method": "pm_1PZrnlE2rDcb2TfPNSULk6Kn",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 3,
                    "exp_year": 2029,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3O8Jokv7ax23Lh2T",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3226",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZrmmE2rDcb2TfP1col0NIz",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZrmmE2rDcb2TfP11Y6qPdB",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "33033",
                    "state": null
                },
                "email": null,
                "name": null,
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720346077,
            "currency": "usd",
            "customer": "cus_QQjFxaQx1OUTcH",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "ac54ea55-6f76-4485-a8c4-f4379575c5ec"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZrmmE2rDcb2TfP1OD8A61B",
            "payment_method": "pm_1PZrnlE2rDcb2TfPNSULk6Kn",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "115142",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 3,
                    "exp_year": 2029,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3O8Jokv7ax23Lh2T",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3226",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBqk5wztFUDosFpjql12emLRnUwo_jbPGhwgc3-SiuHKXOxcBVJc7Bh7KcDp44O5fbCqJ37I",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZrCUE2rDcb2TfP06B014Ho",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "idaliaeli99@hotmail.com",
                "name": "Delfa idalia Elizondo",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720343970,
            "currency": "usd",
            "customer": "cus_QQidKhaJ1u0Ewl",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card does not support this type of purchase.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "329dbbd0-803d-497f-a3d4-441013d8daa5"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "transaction_not_allowed",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `transaction_not_allowed`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZrCUE2rDcb2TfP0Sz1XEqF",
            "payment_method": "pm_1PZrFmE2rDcb2TfP8kDuXNPM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 4,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "TbSeRgDgCljy1Heo",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3723",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZp07E2rDcb2TfP1uJGV463",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "56leticia.oriz@gmail.com",
                "name": "Leticia Garcia",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720335583,
            "currency": "usd",
            "customer": "cus_QQgMqPxq5lMxQ2",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "98cba656-753c-44d7-a3c7-4e7ae2f6274b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "previously_declined_do_not_retry",
                "risk_level": "normal",
                "seller_message": "You previously attempted to charge this card. When the customer's bank declined that payment, it directed Stripe to block future attempts.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZp07E2rDcb2TfP19fObM1T",
            "payment_method": "pm_1PZp4VE2rDcb2TfPAeTiWOXQ",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "ZC0qyMFY8pYrr7CA",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8242",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZp07E2rDcb2TfP1h9EFwAD",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "56leticia.oriz@gmail.com",
                "name": "Leticia Garcia",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720335534,
            "currency": "usd",
            "customer": "cus_QQgMqPxq5lMxQ2",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Invalid account.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "98cba656-753c-44d7-a3c7-4e7ae2f6274b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "invalid_account",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `invalid_account`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZp07E2rDcb2TfP19fObM1T",
            "payment_method": "pm_1PZp3hE2rDcb2TfPg6lZCLmJ",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "ZC0qyMFY8pYrr7CA",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8242",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZoWJE2rDcb2TfP0HBUwrgm",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "delacruzmarioapa@gmail.com",
                "name": "Mario Aparicio de la Cruz",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720333463,
            "currency": "usd",
            "customer": "cus_QQfqqyyct3g0F6",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "99b12af8-e678-42d7-bc33-8bd673bf2e56"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZoWJE2rDcb2TfP0pviv47M",
            "payment_method": "pm_1PZoW0E2rDcb2TfPdJRfpADB",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 2,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "dCyIT7mqe1sZGYuP",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8557",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZoUOE2rDcb2TfP123oBdDC",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZoUOE2rDcb2TfP16kwHbr2",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "delacruzmarioapa@gmail.com",
                "name": "Mario Aparicio de la Cruz",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720333445,
            "currency": "usd",
            "customer": "cus_QQfqqyyct3g0F6",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "70607a61-1c0b-449e-a5b7-b61ade27b9ec"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZoUOE2rDcb2TfP1ZLPuQUY",
            "payment_method": "pm_1PZoW0E2rDcb2TfPdJRfpADB",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "369509",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 2,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "dCyIT7mqe1sZGYuP",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8557",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBpG_3JWGITosFrZOUtDMIzW0i6GtTczEiNP6SDxhGiL6EFLNfQ9W6DYrQYJU2tr6Z6m7JLU",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZlXFE2rDcb2TfP0FoABBYA",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 2990,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZlXFE2rDcb2TfP0lBzzcNE",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "karol.guerra.arechavala@hotmail.com",
                "name": "Carolina Eugenia Guerra Arechavala ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720321990,
            "currency": "usd",
            "customer": "cus_QQckMS2IRQkydQ",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "b1dceca7-161d-47cb-a502-a32d567220af"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZlXFE2rDcb2TfP0ax5m1cX",
            "payment_method": "pm_1PZlWFE2rDcb2TfP0sQI4s4R",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2990,
                    "authorization_code": "062627",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 12,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "CkDV3ikdD0F1GQxJ",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6281",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBh3K9PxfHzosFtGOuga3psj_TElq2KzhNQnbDYBEtDz4cml6x4p_EgP6YkqLxSg-kgu_6gM",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZlUaE2rDcb2TfP1OBx4F67",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZlUaE2rDcb2TfP1c1TEEJf",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "karol.guerra.arechavala@hotmail.com",
                "name": "Carolina Eugenia Guerra Arechavala ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720321928,
            "currency": "usd",
            "customer": "cus_QQckMS2IRQkydQ",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "20c24af0-de62-4692-8bee-34391da68900"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZlUaE2rDcb2TfP1gkSDalU",
            "payment_method": "pm_1PZlWFE2rDcb2TfP0sQI4s4R",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "049281",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 12,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "CkDV3ikdD0F1GQxJ",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6281",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBqtH5xEh7TosFrSqhS0VTmGfEbeO_G_yZgcy5vPtfzylaCQ7JHfL31wCxoWzfUYke2TaN4Q",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZcVmE2rDcb2TfP1jwRZXMe",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "ofservsocial@gmail.com",
                "name": "María Matilde Garcia Caballero",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720287438,
            "currency": "usd",
            "customer": "cus_QQTSIl6E1GY6KB",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card does not support this type of purchase.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "2a80634b-f6cd-4273-84f9-241e48b14fde"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "transaction_not_allowed",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `transaction_not_allowed`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZcVmE2rDcb2TfP1NTRLpiz",
            "payment_method": "pm_1PZcXxE2rDcb2TfPKqEfIL05",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unchecked"
                    },
                    "country": "MX",
                    "exp_month": 6,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "MNDl9NcyD7U8Hph4",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6228",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZaSJE2rDcb2TfP0f1k47sq",
            "object": "charge",
            "amount": 3500,
            "amount_captured": 3500,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZaSJE2rDcb2TfP01i0uPMc",
            "billing_details": {
                "address": {
                    "city": "Lewisville",
                    "country": "US",
                    "line1": "2500 King Arthur Blvd",
                    "line2": "Unit 306",
                    "postal_code": "75056",
                    "state": "TX"
                },
                "email": "pastorita@hotmail.com",
                "name": "Monica Sanchez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720279399,
            "currency": "usd",
            "customer": "cus_QQRHfbqRdgyKzM",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "9b3a8cf8-1705-447b-a494-bab4549fc00c"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZaSJE2rDcb2TfP0SMrGFHI",
            "payment_method": "pm_1PZaPNE2rDcb2TfPKboWDxra",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 3500,
                    "authorization_code": "69831Z",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": "pass",
                        "address_postal_code_check": "pass",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "bYRm6jSqVzywZ5vF",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9671",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 3500,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "9671",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBra-HjQ-8DosFv3f73HefW9tQv-zrIsBY_WI0ckFLSk9OrboQ1dFZULs9KCszDze68aDggQ",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZaPnE2rDcb2TfP1HISVVnQ",
            "object": "charge",
            "amount": 3500,
            "amount_captured": 3500,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZaPnE2rDcb2TfP1iWcs0AK",
            "billing_details": {
                "address": {
                    "city": "Lewisville",
                    "country": "US",
                    "line1": "2500 King Arthur Blvd",
                    "line2": "Unit 306",
                    "postal_code": "75056",
                    "state": "TX"
                },
                "email": "pastorita@hotmail.com",
                "name": "Monica Sanchez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720279243,
            "currency": "usd",
            "customer": "cus_QQRHfbqRdgyKzM",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "8dbffd0d-6719-4986-b9e0-a3c5cba57147"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZaPnE2rDcb2TfP1LJwsOaF",
            "payment_method": "pm_1PZaPNE2rDcb2TfPKboWDxra",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 3500,
                    "authorization_code": "50089Z",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": "pass",
                        "address_postal_code_check": "pass",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "bYRm6jSqVzywZ5vF",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9671",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 3500,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "9671",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBjdDvIeE0zosFn9qcBhzbeCNJcIVL0eyC2xAwLq0SMWEwf1UKvRQnLoINbJYmEIO_8iW-yw",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZaPgE2rDcb2TfP1B4nUdtm",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 2990,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZaPgE2rDcb2TfP1kDFjfs6",
            "billing_details": {
                "address": {
                    "city": "Lewisville",
                    "country": "US",
                    "line1": "2500 King Arthur Blvd",
                    "line2": "Unit 306",
                    "postal_code": "75056",
                    "state": "TX"
                },
                "email": "pastorita@hotmail.com",
                "name": "Monica Sanchez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720279236,
            "currency": "usd",
            "customer": "cus_QQRHfbqRdgyKzM",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "8cfb10bf-af89-4a62-aca0-46069b8184bc"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZaPgE2rDcb2TfP1AQ21l61",
            "payment_method": "pm_1PZaPNE2rDcb2TfPKboWDxra",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2990,
                    "authorization_code": "58442Z",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": "pass",
                        "address_postal_code_check": "pass",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "bYRm6jSqVzywZ5vF",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9671",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "9671",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBj7rsCvRZTosFjAlMANcnnbhbxk0lOUzGEu084FW7CnpYh6FNDBDMEl-J5D79uFigzRx7h0",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZaOrE2rDcb2TfP1BcTVeMn",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZaOrE2rDcb2TfP1oeaAXgw",
            "billing_details": {
                "address": {
                    "city": "Lewisville",
                    "country": "US",
                    "line1": "2500 King Arthur Blvd",
                    "line2": "Unit 306",
                    "postal_code": "75056",
                    "state": "TX"
                },
                "email": "pastorita@hotmail.com",
                "name": "Monica Sanchez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720279217,
            "currency": "usd",
            "customer": "cus_QQRHfbqRdgyKzM",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "8c4adf27-99b9-4b94-b6d0-89f07a2468db"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZaOrE2rDcb2TfP1TeHeBEN",
            "payment_method": "pm_1PZaPNE2rDcb2TfPKboWDxra",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "35717Z",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": "pass",
                        "address_postal_code_check": "pass",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "bYRm6jSqVzywZ5vF",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9671",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "9671",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBuYh2uGUezosFguhbWBVWgYTPJvJzygkShgUqcNbWjHrpYSpxLXAPNxkD6vfx_Z8RdZrA50",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZaBxE2rDcb2TfP1d4opmM9",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "mariateresalunacansigno@gmail.com",
                "name": "Maria Teresa Luna Cansigno",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720278385,
            "currency": "usd",
            "customer": "cus_QQR0iHXJ7Ip85o",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "2a5bf176-412a-4cc8-86ac-0210c85efe74"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZaBxE2rDcb2TfP1BxEjg95",
            "payment_method": "pm_1PZaBfE2rDcb2TfPF9ZYA3UU",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 2,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "1hYToMkdVTgcA6ee",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7891",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZa8yE2rDcb2TfP1h8J5hX1",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZa8yE2rDcb2TfP1JlC838j",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "mariateresalunacansigno@gmail.com",
                "name": "Maria Teresa Luna Cansigno",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720278367,
            "currency": "usd",
            "customer": "cus_QQR0iHXJ7Ip85o",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "d402835e-02e2-4e77-ad6d-cfdde661279f"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZa8yE2rDcb2TfP1m9pznEd",
            "payment_method": "pm_1PZaBfE2rDcb2TfPF9ZYA3UU",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "082405",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 2,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "1hYToMkdVTgcA6ee",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7891",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBozXhmba4TosFidkekzWPWhHjVwjZzJYYCzSoj9jSuPpBNRrzVgjkIFmlLEO2F1s6oawQIg",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZa9uE2rDcb2TfP0DjM9GMX",
            "object": "charge",
            "amount": 3500,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "aplicsa@gmail.com",
                "name": "Luis Bernardo Arias Páez",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720278258,
            "currency": "usd",
            "customer": "cus_QQQuasWvLkoyJ4",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "237c3a59-7fd8-4d5b-9350-404210db9ee2"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZa9uE2rDcb2TfP0D76B7RE",
            "payment_method": "pm_1PZa9SE2rDcb2TfPM53plgzI",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 8,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "shtRDkQ3N2SFXYNS",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 3500,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZa9iE2rDcb2TfP0trzlVxW",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 2990,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZa9iE2rDcb2TfP0glZSoCz",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "aplicsa@gmail.com",
                "name": "Luis Bernardo Arias Páez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720278246,
            "currency": "usd",
            "customer": "cus_QQQuasWvLkoyJ4",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "7f7cc794-d081-4d10-ac54-b85c3715cc5e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": "elevated_risk_level",
                "risk_level": "elevated",
                "rule": "manual_review_if_elevated_risk",
                "seller_message": "Stripe evaluated this payment as having elevated risk, and placed it in your manual review queue.",
                "type": "manual_review"
            },
            "paid": true,
            "payment_intent": "pi_3PZa9iE2rDcb2TfP0UBucp9U",
            "payment_method": "pm_1PZa9SE2rDcb2TfPM53plgzI",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2990,
                    "authorization_code": "510761",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 8,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "shtRDkQ3N2SFXYNS",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBu-7xaeRUjosFhN0B0VMPfJXie13kK3mcOgphj5LmYdI3YmwYpkGqDqki-pG-ctzO5cqd24",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZa2qE2rDcb2TfP1X1NS1xb",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZa2qE2rDcb2TfP1ienQ5Tu",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "aplicsa@gmail.com",
                "name": "Luis Bernardo Arias Páez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720278230,
            "currency": "usd",
            "customer": "cus_QQQuasWvLkoyJ4",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "78292077-42ca-4aeb-add8-d71fe6f3b8a1"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZa2qE2rDcb2TfP188QN0Bu",
            "payment_method": "pm_1PZa9SE2rDcb2TfPM53plgzI",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "829744",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 8,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "shtRDkQ3N2SFXYNS",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBpSGzCJLwTosFvV8k-CxOTedxOmDS12qJHHTp3VEFHzs3mE7eWlBZMa10fIR8geBqI07TOM",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZszE2rDcb2TfP0xfnlqXp",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "marpegay@hotmail.com",
                "name": "mario perez gaytan",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720277349,
            "currency": "usd",
            "customer": "cus_QQQkHCO2AiKDS2",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "11266d26-b2ec-4b2a-b793-dc511c7ef61b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZZszE2rDcb2TfP068tiePn",
            "payment_method": "pm_1PZZvFE2rDcb2TfP7JB1zggI",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 11,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "1YRRJ4b3IIgHYoE6",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9906",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZj5E2rDcb2TfP1rjHvpIn",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "33647",
                    "state": null
                },
                "email": "euclidesrenzo@hotmail.com",
                "name": "Jose Renzo ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720276596,
            "currency": "usd",
            "customer": "cus_QQQXAmBzsvsEpC",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "a45b936b-5380-4e0c-8091-11e98d1fb00c"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZZj5E2rDcb2TfP1HbDIjOu",
            "payment_method": "pm_1PZZijE2rDcb2TfPlFvhr9f7",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 8,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "4PKSmsOzNny3TuRY",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6076",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZgrE2rDcb2TfP0mkjATE1",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZZgrE2rDcb2TfP0V0hBgoN",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "33647",
                    "state": null
                },
                "email": "euclidesrenzo@hotmail.com",
                "name": "Jose Renzo ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720276574,
            "currency": "usd",
            "customer": "cus_QQQXAmBzsvsEpC",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "4e61bacb-c963-4c78-ba26-176d2245f097"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZZgrE2rDcb2TfP0KlRYuFW",
            "payment_method": "pm_1PZZijE2rDcb2TfPlFvhr9f7",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "00552C",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 8,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "4PKSmsOzNny3TuRY",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6076",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBmlYszBUmzosFglWe6fCnd1OqYUjNy3Dql9VTBq9lGvdSrB1oOwMlJ_IQppK75rHUYyzn_s",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZTWE2rDcb2TfP1eGzXLUW",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "marpegay@hotmail.com",
                "name": "Mario Pérez Gaytán",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720276166,
            "currency": "usd",
            "customer": "cus_QQQKNmstG8bjKW",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "27c5b730-ef05-437f-870f-cc2462dbb8b9"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZZTWE2rDcb2TfP1rtKpEO7",
            "payment_method": "pm_1PZZc9E2rDcb2TfPzWslFmMh",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 11,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "1YRRJ4b3IIgHYoE6",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9906",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZTWE2rDcb2TfP1uk57kOH",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "marpegay@hotmail.com",
                "name": "Mario Pérez Gaytán",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720276070,
            "currency": "usd",
            "customer": "cus_QQQKNmstG8bjKW",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "27c5b730-ef05-437f-870f-cc2462dbb8b9"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZZTWE2rDcb2TfP1rtKpEO7",
            "payment_method": "pm_1PZZacE2rDcb2TfPG3Tw3vZ0",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 11,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "1YRRJ4b3IIgHYoE6",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9906",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZTWE2rDcb2TfP1AfDcGJC",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "marpegay@hotmail.com",
                "name": "Mario Pérez Gaytán",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720276009,
            "currency": "usd",
            "customer": "cus_QQQKNmstG8bjKW",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "incorrect_cvc",
            "failure_message": "Your card's security code is incorrect.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "27c5b730-ef05-437f-870f-cc2462dbb8b9"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "incorrect_cvc",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `incorrect_cvc`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZZTWE2rDcb2TfP1rtKpEO7",
            "payment_method": "pm_1PZZZcE2rDcb2TfPFX7EwozY",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "fail"
                    },
                    "country": "MX",
                    "exp_month": 11,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "1YRRJ4b3IIgHYoE6",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "9906",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZIDE2rDcb2TfP1DB2uzwp",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "33647",
                    "state": null
                },
                "email": "euclidesrenzo@hotmail.com",
                "name": "José Renzo",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720275155,
            "currency": "usd",
            "customer": "cus_QQQ8NzIBpdbmAL",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card has insufficient funds.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "ffa58d3b-b6d4-403f-b3e9-3d9d75c44edf"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "insufficient_funds",
                "risk_level": "elevated",
                "seller_message": "The bank returned the decline code `insufficient_funds`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZZIDE2rDcb2TfP170onYwd",
            "payment_method": "pm_1PZZLqE2rDcb2TfPpy9rkeuf",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 5,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "keJnymWGYg5Bn0IC",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "2854",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZClE2rDcb2TfP1wKFENuy",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "mariafelicitaspimentelramirez@gmail.com",
                "name": "Maria Felicitas  Pimentel  Ramirez ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720274591,
            "currency": "usd",
            "customer": "cus_QQPuOjPKeTp1lU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "54ed20e2-ac6d-4fa7-8d49-5e7c1dd4de51"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZZClE2rDcb2TfP1lxwi5lJ",
            "payment_method": "pm_1PZZC2E2rDcb2TfPYrnFxQro",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 9,
                    "exp_year": 2024,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "d5mlaWOp3ejNmKyM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8790",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZ4DE2rDcb2TfP0sc2ZFan",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZZ4DE2rDcb2TfP0FWaWahs",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "mariafelicitaspimentelramirez@gmail.com",
                "name": "Maria Felicitas  Pimentel  Ramirez ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720274546,
            "currency": "usd",
            "customer": "cus_QQPuOjPKeTp1lU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "65538514-c987-4c6e-af69-a4436002e064"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZZ4DE2rDcb2TfP0qdy8xMG",
            "payment_method": "pm_1PZZC2E2rDcb2TfPYrnFxQro",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "025525",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 9,
                    "exp_year": 2024,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "d5mlaWOp3ejNmKyM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8790",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBgMDE79xITosFrnR5Ky00L5UxhHBx-vJyimWpLv0HVW9YyRtr9EnoWe5-OOQrCz-YRkrjP4",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZZ2dE2rDcb2TfP12UnsHms",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "caromarmata@hotmail.com",
                "name": "Carolina Martínez Mata ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720273963,
            "currency": "usd",
            "customer": "cus_QQPmOVSYlRVfY0",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "e3e89d66-d745-4821-abcb-191f4ca87c58"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZZ2dE2rDcb2TfP1WQ2h9Od",
            "payment_method": "pm_1PZZ1tE2rDcb2TfPSMUBvYJ8",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 12,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "FVIYty5wSqrdJIuK",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5339",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZYwzE2rDcb2TfP1uHFIRcP",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZYwzE2rDcb2TfP1fMWRUUW",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "caromarmata@hotmail.com",
                "name": "Carolina Martínez Mata ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720273937,
            "currency": "usd",
            "customer": "cus_QQPmOVSYlRVfY0",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "7f26a4bd-89d0-489b-b705-600c024930e8"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZYwzE2rDcb2TfP1P1CPE2x",
            "payment_method": "pm_1PZZ1tE2rDcb2TfPSMUBvYJ8",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "884652",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 12,
                    "exp_year": 2025,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "FVIYty5wSqrdJIuK",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5339",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBsa5Gh752TosFstZX5N06_o3VhJkd3BVUSwnuNp4H7xKThiCuvKWndI_NmWg-PuhU_DmCTA",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZX36E2rDcb2TfP1HzngRW0",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": "West Palm Beach",
                    "country": "US",
                    "line1": "2682 Oklahoma St",
                    "line2": "",
                    "postal_code": "33406",
                    "state": "FL"
                },
                "email": "aracelysramos@bellsouth.net",
                "name": "Aracelys Llanes",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": false,
            "created": 1720266304,
            "currency": "usd",
            "customer": "cus_QQNjJpPCZ9cRry",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "36658516-b3da-4159-b36d-a44c9d88d347"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "do_not_honor",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `do_not_honor`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZX36E2rDcb2TfP1mzysY3T",
            "payment_method": "pm_1PZX2jE2rDcb2TfPGLms9XSM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": "pass",
                        "address_postal_code_check": "pass",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2031,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "u3OEFkHoJ0IOy7OR",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "1517",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "3373",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZWyBE2rDcb2TfP0AKF7y2H",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZWyBE2rDcb2TfP0wxmjUAM",
            "billing_details": {
                "address": {
                    "city": "West Palm Beach",
                    "country": "US",
                    "line1": "2682 Oklahoma St",
                    "line2": "",
                    "postal_code": "33406",
                    "state": "FL"
                },
                "email": "aracelysramos@bellsouth.net",
                "name": "Aracelys Llanes",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720266281,
            "currency": "usd",
            "customer": "cus_QQNjJpPCZ9cRry",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "43ad5740-f660-47e8-b9e5-aa4987091fe4"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZWyBE2rDcb2TfP04nBAXPp",
            "payment_method": "pm_1PZX2jE2rDcb2TfPGLms9XSM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "024407",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": "pass",
                        "address_postal_code_check": "pass",
                        "cvc_check": null
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2031,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "u3OEFkHoJ0IOy7OR",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "1517",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": {
                        "apple_pay": {
                            "type": "apple_pay"
                        },
                        "dynamic_last4": "3373",
                        "type": "apple_pay"
                    }
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBs3I9d0LJzosFmcAk6Cl21tm68t9cxdMdQL4da6h4yOFzYlxtHRqHfSqT24tRyRv1kMaOVQ",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZTPVE2rDcb2TfP0pzNDd5r",
            "object": "charge",
            "amount": 3500,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "pilybarrios@gmail.com",
                "name": "María del Pilar Barrios ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720252318,
            "currency": "usd",
            "customer": "cus_QQJyIUU2eO33UU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "c618f391-1fde-4c95-99bd-4825759f3a2d"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZTPVE2rDcb2TfP0kHVw3b8",
            "payment_method": "pm_1PZTNkE2rDcb2TfPy7JJuzFM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 1,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "dbkUVKfJjmpe3KUD",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7631",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 3500,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZTOJE2rDcb2TfP0u5SB1nl",
            "object": "charge",
            "amount": 3500,
            "amount_captured": 3500,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZTOJE2rDcb2TfP0vZTvG5J",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "pilybarrios@gmail.com",
                "name": "María del Pilar Barrios ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720252244,
            "currency": "usd",
            "customer": "cus_QQJyIUU2eO33UU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "8280a351-2b0b-4850-9db9-698959578df3"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZTOJE2rDcb2TfP03Fz50B5",
            "payment_method": "pm_1PZTNkE2rDcb2TfPy7JJuzFM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 3500,
                    "authorization_code": "556105",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 1,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "dbkUVKfJjmpe3KUD",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7631",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 3500,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBhxpZd4VfzosFhzXcSTHaDhfhqPUVFqprmcnbBBHK6ABQULHr1PM8Q-cjUx7UVc-A7y_aEk",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZTO6E2rDcb2TfP12FvkBOH",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 2990,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZTO6E2rDcb2TfP1oM3Zu6x",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "pilybarrios@gmail.com",
                "name": "María del Pilar Barrios ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720252230,
            "currency": "usd",
            "customer": "cus_QQJyIUU2eO33UU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "a75cb8c8-5bd4-4577-a0c6-bdfbc3052075"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZTO6E2rDcb2TfP1nwwmnb9",
            "payment_method": "pm_1PZTNkE2rDcb2TfPy7JJuzFM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2990,
                    "authorization_code": "555694",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 1,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "dbkUVKfJjmpe3KUD",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7631",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBiBAN3pLEjosFvphElL9HjBfS2C-Vput8Gy0JR5UKUnwjc5l19L4CtnIbBZyfFvcG1fpNm8",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZTL1E2rDcb2TfP0SM366dI",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZTL1E2rDcb2TfP0uwOPl8n",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "pilybarrios@gmail.com",
                "name": "María del Pilar Barrios ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720252208,
            "currency": "usd",
            "customer": "cus_QQJyIUU2eO33UU",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "bb620f5a-8137-468a-b002-845f78005578"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZTL1E2rDcb2TfP0OuuvCQh",
            "payment_method": "pm_1PZTNkE2rDcb2TfPy7JJuzFM",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "555071",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 1,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "dbkUVKfJjmpe3KUD",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7631",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBoLULGPqczosFvV_EjZCndCnOkmDYBPsiBxMhDvu7qjJNOcHUqrkIpbD_yszCCs4RfXyj64",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZRZtE2rDcb2TfP1l0WZePX",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZRZtE2rDcb2TfP14wCEuKi",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "odijitre@gmail.com",
                "name": "Odilon Jimenez Trejo",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720245485,
            "currency": "usd",
            "customer": "cus_QQIAtmD13mO3vp",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "c233cf48-2fb4-4a96-9cd6-36227e79b5a1"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZRZtE2rDcb2TfP19Zbh9qn",
            "payment_method": "pm_1PZRdJE2rDcb2TfPMph3vZco",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "254777",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 4,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "jhUXMFhsfLTn63zC",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8086",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBs9um8Nz-TosFkj_WvAOo13bkOA6nrq_oxmGVgWyjUsTHk8cXuJugYT3ZMem4fOs2nEZ2xc",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZQKOE2rDcb2TfP0k33MftU",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "turbospraymexico@gmail.com",
                "name": "Alejandro Garcia",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720240751,
            "currency": "usd",
            "customer": "cus_QQGsIL1uO35tXM",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "2ac88903-06aa-4d14-a303-243390d4275e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZQKOE2rDcb2TfP0fVIkIz9",
            "payment_method": "pm_1PZQOwE2rDcb2TfPrX7FPJlx",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "j6w3loGTrDsMxx7N",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0110",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZQLBE2rDcb2TfP0vDM3hjN",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "90201",
                    "state": null
                },
                "email": "aidammartinez0720@gmail.com",
                "name": "Aida",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720240724,
            "currency": "usd",
            "customer": "cus_QQGsWdb2M59txu",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "d9ee4165-2c02-42cc-a5ac-a318a0246348"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "try_again_later",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `try_again_later`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZQLBE2rDcb2TfP02tGxt8l",
            "payment_method": "pm_1PZQOVE2rDcb2TfPwfCkCOts",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "Gd66O4bBQhqpYuT3",
                    "funding": "prepaid",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "1447",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZQKOE2rDcb2TfP0BHoG8cD",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "turbospraymexico@gmail.com",
                "name": "Alejandro Garcia",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720240638,
            "currency": "usd",
            "customer": "cus_QQGsIL1uO35tXM",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card does not support this type of purchase.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "2ac88903-06aa-4d14-a303-243390d4275e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "transaction_not_allowed",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `transaction_not_allowed`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PZQKOE2rDcb2TfP0fVIkIz9",
            "payment_method": "pm_1PZQN7E2rDcb2TfP8RNphLPH",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 12,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "j6w3loGTrDsMxx7N",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0110",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZCMlE2rDcb2TfP0J4v74QK",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "terapeuta.leticia.torres@gmail.com",
                "name": "María LeticiaTorres",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720186799,
            "currency": "usd",
            "customer": "cus_QQ2OxrVoSSxxzV",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "1e19199a-e410-46a2-8db0-1d57ede199da"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZCMlE2rDcb2TfP0idYU2Dv",
            "payment_method": "pm_1PZCLCE2rDcb2TfPEFZcZEdO",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3ruGXeSS8ejgRaoq",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0126",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZCLiE2rDcb2TfP1X82C6TS",
            "object": "charge",
            "amount": 3500,
            "amount_captured": 3500,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZCLiE2rDcb2TfP1mGfdGnp",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "terapeuta.leticia.torres@gmail.com",
                "name": "María LeticiaTorres",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720186734,
            "currency": "usd",
            "customer": "cus_QQ2OxrVoSSxxzV",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "4559d7c6-6a69-4802-ad45-700106c4db2b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": "elevated_risk_level",
                "risk_level": "elevated",
                "rule": "manual_review_if_elevated_risk",
                "seller_message": "Stripe evaluated this payment as having elevated risk, and placed it in your manual review queue.",
                "type": "manual_review"
            },
            "paid": true,
            "payment_intent": "pi_3PZCLiE2rDcb2TfP11ISca35",
            "payment_method": "pm_1PZCLCE2rDcb2TfPEFZcZEdO",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 3500,
                    "authorization_code": "050469",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3ruGXeSS8ejgRaoq",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0126",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 3500,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBsJmQawpeTosFthlHljeZ_6KXEVcubTaEigp_diT-yGr74LT30q-SfGD5ZtWtaPti9b94Us",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZCLUE2rDcb2TfP0pqJcuzr",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 2990,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZCLUE2rDcb2TfP0z7oa52O",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "terapeuta.leticia.torres@gmail.com",
                "name": "María LeticiaTorres",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720186720,
            "currency": "usd",
            "customer": "cus_QQ2OxrVoSSxxzV",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "05d97db9-a574-474d-9144-c5bc88a1ed45"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZCLUE2rDcb2TfP0xqi8EIR",
            "payment_method": "pm_1PZCLCE2rDcb2TfPEFZcZEdO",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2990,
                    "authorization_code": "050832",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3ruGXeSS8ejgRaoq",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0126",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBuqODDpfvjosFgBeoh_EZ39A-r-e513RfNgQdyebSN0KnJB_0B4rjEj5tAcie_m2h2Z62Cg",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZCJzE2rDcb2TfP0a0a0y95",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZCJzE2rDcb2TfP0j3O20CL",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "terapeuta.leticia.torres@gmail.com",
                "name": "María LeticiaTorres",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720186702,
            "currency": "usd",
            "customer": "cus_QQ2OxrVoSSxxzV",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "2649998c-f4c4-4fe5-aecd-194e450958a0"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZCJzE2rDcb2TfP0zjrSMvR",
            "payment_method": "pm_1PZCLCE2rDcb2TfPEFZcZEdO",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "078246",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 10,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "3ruGXeSS8ejgRaoq",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0126",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBoBbF-Q6XDosFithEx3eFhtlmXro7q0hOZ0MQsOltOBu2WPGBSctS3tazsNeVfvVYPugpK4",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZBjyE2rDcb2TfP0R0x9Tbc",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "olympia.68@hotmail.com",
                "name": "Ma Olimpia Figueroa ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720184394,
            "currency": "usd",
            "customer": "cus_QQ1YmEDDnUfXom",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "d71d39ca-e6b0-4101-982b-0831a3c0f445"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZBjyE2rDcb2TfP0E6lvn9K",
            "payment_method": "pm_1PZBZUE2rDcb2TfPFg48VmKs",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 3,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "DW8WZTRnZIQX4mzR",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8755",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZBdeE2rDcb2TfP03gOAEah",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "olympia.68@hotmail.com",
                "name": "Ma Olimpia Figueroa ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720184002,
            "currency": "usd",
            "customer": "cus_QQ1YmEDDnUfXom",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "fe9137b5-3abc-432d-9210-ce564010ddea"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZBdeE2rDcb2TfP0x0jFROa",
            "payment_method": "pm_1PZBZUE2rDcb2TfPFg48VmKs",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 3,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "DW8WZTRnZIQX4mzR",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8755",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZBZrE2rDcb2TfP1jSgKfq3",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "olympia.68@hotmail.com",
                "name": "Ma Olimpia Figueroa ",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720183768,
            "currency": "usd",
            "customer": "cus_QQ1YmEDDnUfXom",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "2e44c239-bbc5-4ecc-948e-2315e8016898"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZBZrE2rDcb2TfP10hsusGj",
            "payment_method": "pm_1PZBZUE2rDcb2TfPFg48VmKs",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 3,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "DW8WZTRnZIQX4mzR",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8755",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZBVbE2rDcb2TfP1BXXJW5N",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZBVbE2rDcb2TfP1VsuWECM",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "olympia.68@hotmail.com",
                "name": "Ma Olimpia Figueroa ",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720183745,
            "currency": "usd",
            "customer": "cus_QQ1YmEDDnUfXom",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "fc89e829-b363-4d0c-a135-97169439839a"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZBVbE2rDcb2TfP1jKXbdrs",
            "payment_method": "pm_1PZBZUE2rDcb2TfPFg48VmKs",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "565166",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 3,
                    "exp_year": 2026,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "DW8WZTRnZIQX4mzR",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "8755",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBrqemcwMczosFmjlUcsPSUGwM2G3-c0wxICZf4C6yHEWBjotSN6TrjR4kgktog_WgSPuAPo",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZ3trE2rDcb2TfP0MNl5u5L",
            "object": "charge",
            "amount": 4000,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "coronavazquezanamaria@gmail.com",
                "name": "Ana Maria Corona Vazquez",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1720154255,
            "currency": "usd",
            "customer": "cus_QPtWE5ZpbfLDKp",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {
                "stripe_report": "fraudulent"
            },
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "13caa374-ce17-424c-a57f-18334225b878"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "highest",
                "rule": "block_if_high_risk",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PZ3trE2rDcb2TfP0HESceKX",
            "payment_method": "pm_1PZ3t4E2rDcb2TfPIZ2uEErV",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 5,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "V8YuPqPa9ru9tepL",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6000",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 4000,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZ3tWE2rDcb2TfP0GSVx7sV",
            "object": "charge",
            "amount": 2990,
            "amount_captured": 2990,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZ3tWE2rDcb2TfP0kv4yPXB",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "coronavazquezanamaria@gmail.com",
                "name": "Ana Maria Corona Vazquez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720154234,
            "currency": "usd",
            "customer": "cus_QPtWE5ZpbfLDKp",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "6b31dbae-fc8d-4ccb-8f46-1f9b06132179"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZ3tWE2rDcb2TfP0ekT6Jze",
            "payment_method": "pm_1PZ3t4E2rDcb2TfPIZ2uEErV",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2990,
                    "authorization_code": "014475",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 5,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "V8YuPqPa9ru9tepL",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6000",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2990,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBp07blNRTjosFuAx8j7KsjlgYhErVR3sWpk3Y-rvxxjvylvgO_NcjsbLBczK_5Zdi_nr5jQ",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PZ3jME2rDcb2TfP0lrK2LaI",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PZ3jME2rDcb2TfP0n2F68pS",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "coronavazquezanamaria@gmail.com",
                "name": "Ana Maria Corona Vazquez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720154207,
            "currency": "usd",
            "customer": "cus_QPtWE5ZpbfLDKp",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "ee29e5a8-a0cb-47d5-8fb7-6dc5ed7f688e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PZ3jME2rDcb2TfP0RChNtTo",
            "payment_method": "pm_1PZ3t4E2rDcb2TfPIZ2uEErV",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "094414",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 5,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "V8YuPqPa9ru9tepL",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6000",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBicKPECq6josFn0R28WiErngev_c9M4w_O_koGJ8_5nJVVhpm-a8MDDvURBLEBFmRNRO7bo",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYupcE2rDcb2TfP0z0b56t5",
            "object": "charge",
            "amount": 300,
            "amount_captured": 300,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PYupcE2rDcb2TfP0RQR2CZr",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "BR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "contatoezequieldev@gmail.com",
                "name": "Ezequiel Moraes Mello",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720119396,
            "currency": "usd",
            "customer": "cus_QPk59p0uM4Muju",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "0f58fd8d-c7da-41a4-8129-e24b54b75b9a"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PYupcE2rDcb2TfP0ceDR0De",
            "payment_method": "pm_1PYumEE2rDcb2TfPhWx6W1uo",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 300,
                    "authorization_code": "681970",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "KY",
                    "exp_month": 6,
                    "exp_year": 2033,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "J0EDPYa5BWuVBOtM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0932",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 300,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBvPdG7kpzDosFuPXeasdyb8Pqhp9XSlzvlGB36RSga0nIR2Hna3Kyfw5eCmy1Wqty8INb8I",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYuoaE2rDcb2TfP1Jli62yI",
            "object": "charge",
            "amount": 300,
            "amount_captured": 300,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PYuoaE2rDcb2TfP1hZM3YE6",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "BR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "contatoezequieldev@gmail.com",
                "name": "Ezequiel Moraes Mello",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720119332,
            "currency": "usd",
            "customer": "cus_QPk59p0uM4Muju",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "f5067565-b290-4607-a5ad-73b9c08e8b72"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PYuoaE2rDcb2TfP1XBtb1fr",
            "payment_method": "pm_1PYumEE2rDcb2TfPhWx6W1uo",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 300,
                    "authorization_code": "681330",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "KY",
                    "exp_month": 6,
                    "exp_year": 2033,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "J0EDPYa5BWuVBOtM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0932",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 300,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBva_wC3LXjosFtQ6EtcGDMU5lG0v7vLG3dSOMd819k-W52k66BrDnpKgfuW3miKTjdxkwjo",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYubIE2rDcb2TfP1cToHIA7",
            "object": "charge",
            "amount": 300,
            "amount_captured": 300,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PYubIE2rDcb2TfP1xKwG4dV",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "BR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "contatoezequieldev@gmail.com",
                "name": "Ezequiel Moraes Mello",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720119186,
            "currency": "usd",
            "customer": "cus_QPk59p0uM4Muju",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "7e94d71c-00c8-43b0-8c71-60c33bdf631e"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PYubIE2rDcb2TfP1fQkSU1O",
            "payment_method": "pm_1PYumEE2rDcb2TfPhWx6W1uo",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 300,
                    "authorization_code": "679880",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "KY",
                    "exp_month": 6,
                    "exp_year": 2033,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "J0EDPYa5BWuVBOtM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0932",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 300,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBgTI4J7oWDosFt0u5AQJh-2Szpt1xEwWFaHumMIEjYf4aEBqwvrXPgWZAsfkSeowPwpLrxY",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYq87E2rDcb2TfP0LHI473R",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "alonsovizcanomoreno@gmail.com",
                "name": "Juan Alonso Vizcano Estrada",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": false,
            "created": 1720101324,
            "currency": "usd",
            "customer": "cus_QPfOrDqeFCQcwv",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "61a9ebf2-46de-45f1-9586-be0d9c8898e5"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "generic_decline",
                "risk_level": "normal",
                "seller_message": "The bank did not return any further details with this decline.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PYq87E2rDcb2TfP0TKtSnvt",
            "payment_method": "pm_1PYq6nE2rDcb2TfPEuOp8nW1",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 11,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "Z29CXQ7sn6E24Wkv",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5566",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYq7DE2rDcb2TfP0lP1w742",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PYq7DE2rDcb2TfP0ECuyRCz",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "alonsovizcanomoreno@gmail.com",
                "name": "Juan Alonso Vizcano Estrada",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS* UPSELL",
            "captured": true,
            "created": 1720101267,
            "currency": "usd",
            "customer": "cus_QPfOrDqeFCQcwv",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "3e09f20f-baab-4b23-acdc-d2a29d477ef8"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PYq7DE2rDcb2TfP09zuUl3R",
            "payment_method": "pm_1PYq6nE2rDcb2TfPEuOp8nW1",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "042830",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 11,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "Z29CXQ7sn6E24Wkv",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5566",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBlnfTfJFwDosFgvce-ZR78rRrCpjYiNhm-iAHXKLeeyITvzp6g8J0cyu98wuaADJ3-g4PWQ",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": "UPSELL",
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYq3uE2rDcb2TfP1XUAcRub",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PYq3uE2rDcb2TfP1Ag31t0i",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "alonsovizcanomoreno@gmail.com",
                "name": "Juan Alonso Vizcano Estrada",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720101242,
            "currency": "usd",
            "customer": "cus_QPfOrDqeFCQcwv",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "0382441f-bb7c-41b0-b39d-f3302e30bf2c"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PYq3uE2rDcb2TfP1OWGIw5D",
            "payment_method": "pm_1PYq6nE2rDcb2TfPEuOp8nW1",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "040204",
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "pass"
                    },
                    "country": "MX",
                    "exp_month": 11,
                    "exp_year": 2028,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "Z29CXQ7sn6E24Wkv",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "5566",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBiiCOsbQSDosFkRTwaFL3b9RfEZ6FLe2kAv-oXlLmpbVxeBj5mRbOc6rBavXpjt5k1bXJus",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYnzVE2rDcb2TfP1BCT2LrU",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 2700,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3PYnzVE2rDcb2TfP1EeF7uKq",
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "33177",
                    "state": null
                },
                "email": "robertomart58@gmail.com",
                "name": "Roberto martinez",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": true,
            "created": 1720093317,
            "currency": "usd",
            "customer": "cus_QPdGBhjlQwCSoT",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "3b80951a-07c1-411d-9bc6-584c12c5daeb"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "approved_by_network",
                "reason": null,
                "risk_level": "normal",
                "seller_message": "Payment complete.",
                "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3PYnzVE2rDcb2TfP1VrWqv4v",
            "payment_method": "pm_1PYo2yE2rDcb2TfPOwzsqglm",
            "payment_method_details": {
                "card": {
                    "amount_authorized": 2700,
                    "authorization_code": "074107",
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 12,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "9gjmGVAxqaVgtt4z",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "6241",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFQVWFkT0UyckRjYjJUZlAo-LaKtQYyBrOJhQJwWTosFmZcPEIwHHuO6dc10_-f7Ybq_ll2e7AiojpksNsZQqMX6Sm7HMrlK0xOWAw",
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PYmdZE2rDcb2TfP1whfcMSq",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "MX",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "antonietamartinezp@hotmail.com",
                "name": "Martinez María Antonieta",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1720088437,
            "currency": "usd",
            "customer": "cus_QPbr9KoXO5jL6p",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "expired_card",
            "failure_message": "Your card has expired.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {
                "order_id": "56d173e1-0ca5-4e2c-9d06-d3f30731a24b"
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "expired_card",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `expired_card`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PYmdZE2rDcb2TfP13fk8xwO",
            "payment_method": "pm_1PYmmHE2rDcb2TfPEmha4MxW",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "MX",
                    "exp_month": 5,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "AUsZnZTzamPftURq",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7810",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PVA8YE2rDcb2TfP08CZvbm1",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "CR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "susy.diaz@gmail.com",
                "name": "SUSANA DIAZ UMAÑA",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719225218,
            "currency": "usd",
            "customer": "cus_QLrsAQa7bjxe11",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PVA8YE2rDcb2TfP0ihlcHmj",
            "payment_method": "pm_1PVADNE2rDcb2TfPLn8ISj1Q",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "CR",
                    "exp_month": 12,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "riotw8SUcKkF3KEy",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4135",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PVA8YE2rDcb2TfP0WcHHkGf",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "CR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "susy.diaz@gmail.com",
                "name": "SUSANA DIAZ UMAÑA",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719225188,
            "currency": "usd",
            "customer": "cus_QLrsAQa7bjxe11",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PVA8YE2rDcb2TfP0ihlcHmj",
            "payment_method": "pm_1PVACtE2rDcb2TfPLmf2V4P9",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "CR",
                    "exp_month": 12,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "riotw8SUcKkF3KEy",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4135",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PVA8YE2rDcb2TfP0SqP9mIH",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "CR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "susy.diaz@gmail.com",
                "name": "SUSANA DIAZ UMAÑA",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719225089,
            "currency": "usd",
            "customer": "cus_QLrsAQa7bjxe11",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PVA8YE2rDcb2TfP0ihlcHmj",
            "payment_method": "pm_1PVABIE2rDcb2TfPSzxDJPEH",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "CR",
                    "exp_month": 12,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "PEiepQ9OyevH5BfA",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "2872",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PVA8YE2rDcb2TfP0gIGYETT",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "CR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "susy.diaz@gmail.com",
                "name": "Susana Díaz Umaña",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719224925,
            "currency": "usd",
            "customer": "cus_QLrsAQa7bjxe11",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PVA8YE2rDcb2TfP0ihlcHmj",
            "payment_method": "pm_1PVA8RE2rDcb2TfPiJvSDICT",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "CR",
                    "exp_month": 5,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "DHPgi3Uyd9cutjHv",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "0015",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV9jgE2rDcb2TfP1seNHd7Q",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "AR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "carlitomartinez51@gmail.com",
                "name": "Martinez Juan Carlos",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719223591,
            "currency": "usd",
            "customer": "cus_QLrS843G4i3z0B",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PV9jgE2rDcb2TfP1Nz1tTN8",
            "payment_method": "pm_1PV9n8E2rDcb2TfPlaMr7aWr",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "AR",
                    "exp_month": 1,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "jUib261ilFQywNZ3",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV9jgE2rDcb2TfP1aov14fQ",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "AR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "carlitomartinez51@gmail.com",
                "name": "Juan Carlos Martinez",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719223554,
            "currency": "usd",
            "customer": "cus_QLrS843G4i3z0B",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PV9jgE2rDcb2TfP1Nz1tTN8",
            "payment_method": "pm_1PV9mWE2rDcb2TfPqMHp3YZm",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "AR",
                    "exp_month": 1,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "jUib261ilFQywNZ3",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV9jgE2rDcb2TfP1ViMcTSD",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "AR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "carlitomartinez51@gmail.com",
                "name": "Juan Carlos Martinez",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719223528,
            "currency": "usd",
            "customer": "cus_QLrS843G4i3z0B",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PV9jgE2rDcb2TfP1Nz1tTN8",
            "payment_method": "pm_1PV9m7E2rDcb2TfPYzWmNnUu",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "AR",
                    "exp_month": 1,
                    "exp_year": 2030,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "jUib261ilFQywNZ3",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4759",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV9jgE2rDcb2TfP1DmfoSBZ",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "AR",
                    "line1": null,
                    "line2": null,
                    "postal_code": null,
                    "state": null
                },
                "email": "carlitomartinez51@gmail.com",
                "name": "Juan Carlos Martinez",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719223377,
            "currency": "usd",
            "customer": "cus_QLrS843G4i3z0B",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PV9jgE2rDcb2TfP1Nz1tTN8",
            "payment_method": "pm_1PV9jfE2rDcb2TfPRGp1pQbq",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": null,
                        "cvc_check": "unavailable"
                    },
                    "country": "AR",
                    "exp_month": 3,
                    "exp_year": 2029,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "xhjMN9NYJSTkuNDa",
                    "funding": "credit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "4727",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV7IKE2rDcb2TfP0ZDkpqd2",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "99336",
                    "state": null
                },
                "email": "ismaeltovar89@gmail.com",
                "name": "Ismael d tovar",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719214279,
            "currency": "usd",
            "customer": "cus_QLowW9MXmXJBQ9",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PV7IKE2rDcb2TfP0hO4SaZw",
            "payment_method": "pm_1PV7MwE2rDcb2TfPetgMehPZ",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "QXarRBdPnM7Qwv8L",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7504",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV7IKE2rDcb2TfP07yp37Mk",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "99336",
                    "state": null
                },
                "email": "ismaeltovar89@gmail.com",
                "name": "Ismael d tovar",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719214208,
            "currency": "usd",
            "customer": "cus_QLowW9MXmXJBQ9",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PV7IKE2rDcb2TfP0hO4SaZw",
            "payment_method": "pm_1PV7LnE2rDcb2TfPWQnRKiK6",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "QXarRBdPnM7Qwv8L",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7504",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV7IKE2rDcb2TfP0HemjFRw",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "99336",
                    "state": null
                },
                "email": "ismaeltovar89@gmail.com",
                "name": "Ismael d tovar",
                "phone": null
            },
            "calculated_statement_descriptor": null,
            "captured": false,
            "created": 1719214147,
            "currency": "usd",
            "customer": "cus_QLowW9MXmXJBQ9",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "not_sent_to_network",
                "reason": "highest_risk_level",
                "risk_level": "normal",
                "seller_message": "Stripe blocked this payment as too risky.",
                "type": "blocked"
            },
            "paid": false,
            "payment_intent": "pi_3PV7IKE2rDcb2TfP0hO4SaZw",
            "payment_method": "pm_1PV7KoE2rDcb2TfPXq8qycbD",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "unavailable",
                        "cvc_check": "unavailable"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "QXarRBdPnM7Qwv8L",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7504",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV7IKE2rDcb2TfP0dgtO0Cu",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "99336",
                    "state": null
                },
                "email": "ismaeltovar89@gmail.com",
                "name": "Ismael d tovar",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1719213992,
            "currency": "usd",
            "customer": "cus_QLowW9MXmXJBQ9",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "do_not_honor",
                "risk_level": "normal",
                "seller_message": "The bank returned the decline code `do_not_honor`.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PV7IKE2rDcb2TfP0hO4SaZw",
            "payment_method": "pm_1PV7IJE2rDcb2TfP3YmGM4OC",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "visa",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "QXarRBdPnM7Qwv8L",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "7504",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "visa",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": null,
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV6IBE2rDcb2TfP0dJug2xk",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "60505",
                    "state": null
                },
                "email": "marthachong11@gmail.com",
                "name": "MARTHA CHONG",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1719210375,
            "currency": "usd",
            "customer": "cus_QLntz5cs8tDB9m",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "generic_decline",
                "risk_level": "normal",
                "seller_message": "The bank did not return any further details with this decline.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PV6IBE2rDcb2TfP06OBVSpC",
            "payment_method": "pm_1PV6LsE2rDcb2TfP58zQhFmH",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "VGofRNYp6hYa96zM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3264",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": {
                        "authentication_flow": "frictionless",
                        "electronic_commerce_indicator": "02",
                        "exemption_indicator": null,
                        "result": "authenticated",
                        "result_reason": null,
                        "transaction_id": "4f51e30c-7651-449b-bf43-f64004119a41",
                        "version": "2.2.0"
                    },
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV6IBE2rDcb2TfP0moGUPhM",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "60505",
                    "state": null
                },
                "email": "marthachong11@gmail.com",
                "name": "MARTHA CHONG",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1719210301,
            "currency": "usd",
            "customer": "cus_QLntz5cs8tDB9m",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "generic_decline",
                "risk_level": "normal",
                "seller_message": "The bank did not return any further details with this decline.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PV6IBE2rDcb2TfP06OBVSpC",
            "payment_method": "pm_1PV6KgE2rDcb2TfP8fXVsumk",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "VGofRNYp6hYa96zM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3264",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": {
                        "authentication_flow": "frictionless",
                        "electronic_commerce_indicator": "02",
                        "exemption_indicator": null,
                        "result": "authenticated",
                        "result_reason": null,
                        "transaction_id": "c5aea8d9-b22a-4871-84fa-aa88e7dae11b",
                        "version": "2.2.0"
                    },
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        },
        {
            "id": "ch_3PV6IBE2rDcb2TfP09W6T1pz",
            "object": "charge",
            "amount": 2700,
            "amount_captured": 0,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": null,
            "billing_details": {
                "address": {
                    "city": null,
                    "country": "US",
                    "line1": null,
                    "line2": null,
                    "postal_code": "60505",
                    "state": null
                },
                "email": "marthachong11@gmail.com",
                "name": "MARTHA CHONG",
                "phone": null
            },
            "calculated_statement_descriptor": "ALLOPS.COM.BR",
            "captured": false,
            "created": 1719210147,
            "currency": "usd",
            "customer": "cus_QLntz5cs8tDB9m",
            "description": null,
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_balance_transaction": null,
            "failure_code": "card_declined",
            "failure_message": "Your card was declined.",
            "fraud_details": {},
            "invoice": null,
            "livemode": true,
            "metadata": {},
            "on_behalf_of": null,
            "order": null,
            "outcome": {
                "network_status": "declined_by_network",
                "reason": "generic_decline",
                "risk_level": "normal",
                "seller_message": "The bank did not return any further details with this decline.",
                "type": "issuer_declined"
            },
            "paid": false,
            "payment_intent": "pi_3PV6IBE2rDcb2TfP06OBVSpC",
            "payment_method": "pm_1PV6I9E2rDcb2TfPeimPWGDc",
            "payment_method_details": {
                "card": {
                    "amount_authorized": null,
                    "authorization_code": null,
                    "brand": "mastercard",
                    "checks": {
                        "address_line1_check": null,
                        "address_postal_code_check": "pass",
                        "cvc_check": "pass"
                    },
                    "country": "US",
                    "exp_month": 6,
                    "exp_year": 2027,
                    "extended_authorization": {
                        "status": "disabled"
                    },
                    "fingerprint": "VGofRNYp6hYa96zM",
                    "funding": "debit",
                    "incremental_authorization": {
                        "status": "unavailable"
                    },
                    "installments": null,
                    "last4": "3264",
                    "mandate": null,
                    "multicapture": {
                        "status": "unavailable"
                    },
                    "network": "mastercard",
                    "network_token": {
                        "used": false
                    },
                    "overcapture": {
                        "maximum_amount_capturable": 2700,
                        "status": "unavailable"
                    },
                    "three_d_secure": {
                        "authentication_flow": "frictionless",
                        "electronic_commerce_indicator": "02",
                        "exemption_indicator": null,
                        "result": "authenticated",
                        "result_reason": null,
                        "transaction_id": "0c2d3912-4d65-45b1-bbc2-b93e4597dfc7",
                        "version": "2.2.0"
                    },
                    "wallet": null
                },
                "type": "card"
            },
            "radar_options": {},
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": null,
            "refunded": false,
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "failed",
            "transfer_data": null,
            "transfer_group": null
        }
    ],
    "has_more": false,
    "url": "/v1/charges"
}
EOF;

        \Stripe\Stripe::setAppInfo(
            "stripe-samples/accept-a-payment/payment-element",
            "0.0.2",
            "https://github.com/stripe-samples"
        );

        $stripe = new \Stripe\StripeClient([
            'api_key' => env('STRIPE_SECRET_PROD'),
            'stripe_version' => '2023-10-16',
        ]);

        $arr = json_decode($charges);
        echo '<pre>';
        $n = 0;
        foreach ($arr->data as $obj)
        {
            if (!$obj->paid) continue;

            $amount_brl = $obj->amount;

            if ($obj->currency == 'usd') $amount_brl = intval($amount_brl * 5);
            if ($obj->currency == 'eur') $amount_brl = intval($amount_brl * 5.5);

            print_r($obj->id . ": R$ " . number_format($amount_brl / 100, 2, ',', '.'));
            print_r("\n");

            $transfer = '';
            try
            {
                $transfer = $stripe->transfers->create([
                    'amount' => $amount_brl,
                    'currency' => 'brl',
                    'destination' => 'acct_1Pf2o9CsFYDMOhVy',
                    'source_transaction' => $obj->id,
                ]);
            }
            catch (\Stripe\Exception\InvalidRequestException $ex)
            {
                echo $ex->getMessage();
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage();
            }

            print_r("\n");
            print_r($transfer);
            print_r("\n\n-------------------------\n\n");
            $n++;
        }
    }

    public function queue_stripe_webhook()
    {
        $payload = <<<EOF
{
    "id": "evt_3PHvWe04Gx7pql3l1QN4kgBE",
    "object": "event",
    "api_version": "2023-10-16",
    "created": 1716070578,
    "data": {
        "object": {
        "id": "ch_3PHvWe04Gx7pql3l1Fxu2jQw",
        "object": "charge",
        "amount": 500,
        "amount_captured": 500,
        "amount_refunded": 0,
        "application": null,
        "application_fee": null,
        "application_fee_amount": null,
        "balance_transaction": "txn_3PHvWe04Gx7pql3l1nBBvBHe",
        "billing_details": {
            "address": {
            "city": null,
            "country": "BR",
            "line1": null,
            "line2": null,
            "postal_code": null,
            "state": null
            },
            "email": "Gay@gmail.com",
            "name": "Sou gay",
            "phone": null
        },
        "calculated_statement_descriptor": "ZILZCORP",
        "captured": true,
        "created": 1716070576,
        "currency": "usd",
        "customer": null,
        "description": null,
        "destination": null,
        "dispute": null,
        "disputed": false,
        "failure_balance_transaction": null,
        "failure_code": null,
        "failure_message": null,
        "fraud_details": {
        },
        "invoice": null,
        "livemode": true,
        "metadata": {
            "order_id": "73093fee-75fd-4664-a6e9-545e1000627f"
        },
        "on_behalf_of": null,
        "order": null,
        "outcome": {
            "network_status": "approved_by_network",
            "reason": null,
            "risk_level": "normal",
            "seller_message": "Payment complete.",
            "type": "authorized"
        },
        "paid": true,
        "payment_intent": "pi_3PJM8pEJPXmFR8ZQ1fv2n9P8",
        "payment_method": "pm_1PHvY304Gx7pql3leHUCpgKe",
        "payment_method_details": {
            "card": {
            "amount_authorized": 500,
            "brand": "mastercard",
            "checks": {
                "address_line1_check": null,
                "address_postal_code_check": null,
                "cvc_check": "pass"
            },
            "country": "US",
            "exp_month": 5,
            "exp_year": 2028,
            "extended_authorization": {
                "status": "disabled"
            },
            "fingerprint": "A8eLAy2yiMgRS4Rn",
            "funding": "debit",
            "incremental_authorization": {
                "status": "unavailable"
            },
            "installments": null,
            "last4": "7783",
            "mandate": null,
            "multicapture": {
                "status": "unavailable"
            },
            "network": "mastercard",
            "network_token": {
                "used": false
            },
            "overcapture": {
                "maximum_amount_capturable": 500,
                "status": "unavailable"
            },
            "three_d_secure": null,
            "wallet": null
            },
            "type": "card"
        },
        "radar_options": {
        },
        "receipt_email": null,
        "receipt_number": null,
        "receipt_url": "https://pay.stripe.com/receipts/payment/CAcQARoXChVhY2N0XzFPeElpbzA0R3g3cHFsM2wos9GksgYyBi2FwfeVWTosFlbeHYbjNQcmqVaoaWMPQKMxMdNQo-SUyBUnTg7qsep-vdfhNgS49eFpV2A",
        "refunded": false,
        "review": null,
        "shipping": null,
        "source": null,
        "source_transfer": null,
        "statement_descriptor": null,
        "statement_descriptor_suffix": null,
        "status": "succeeded",
        "transfer_data": null,
        "transfer_group": null
        }
    },
    "livemode": true,
    "pending_webhooks": 1,
    "request": {
        "id": "req_p2iCvK3EXl79q1",
        "idempotency_key": "72fc4c3b-a7ef-475b-bb81-70df13515b7e"
    },
    "type": "charge.succeeded"
    }
EOF;

        StripeWebhookQueue::push($payload, date("Y-m-d H:i:s", strtotime(today() . " + 30 seconds")), true);
    }

    public function webhook_stripe(Request $request)
    {
        StripeWebhookQueue::receive(new StripeWebhookType([
            "data" => $request->raw(),
            "bypass" => true,
            "entity" => null
        ]));
    }

    public function utmify(Request $request)
    {
        UtmifyQueue::send(new UtmifyType([
            "data" => $request->raw(),
            "entity" => null
        ]));
    }

    public function utmify_entity(Request $request, $id)
    {
        UtmifyQueue::send(new UtmifyType([
            "data" => $request->raw(),
            "entity" => ModelsUtmifyQueue::find($id)
        ]));
    }

    public function iugu_charge__update_items(Request $request, $user_id)
    {

    }

    public function iugu_charge__list_items(Request $request, $user_id, $api_token)
    {
        
    }

    public function iugu_charge__queue_push(Request $request, $order_id, $customer_id)
    {
        $customer = Customer::find($customer_id);
        $order = Order::find($order_id);
        
        $subscription = new Subscription;
        $subscription->status = ESubscriptionStatus::PENDING;
        $subscription->customer_id = $customer->id;
        $subscription->order_id = $order->id;
        $subscription->interval = 'month';
        $subscription->interval_count = 1;
        $subscription->save();

        $subscription_expires_at = date("Y-m-d H:i:s", strtotime(today() . " + $subscription->interval_count $subscription->interval"));
        
        $invoice = new Invoice;
        $invoice->order_id = $order->id;
        $invoice->due_date = $subscription_expires_at;
        $invoice->paid = false;
        $invoice->save();

        $headers = ['Content-Type' => 'application/json'];

        $payload_token = [
            'account_id' => env('IUGU_ACCOUNT_ID'),
            'method' => 'credit_card',
            'test' => 'true',
            'data' => [
                'number' => '4242424242424242',
                'verification_value' => '123',
                'first_name' => 'Jose',
                'last_name' => 'da Silva',
                'month' => '12',
                'year' => '34'
            ]
        ];

        $payload_charge = [
            'token' => uniqid(),
            'keep_dunning' => true,
            'items' => [
                'description' => 'Produto teste',
                'quantity' => 1,
                'price_cents' => 200
            ],
            'payer' => [
                'name' => 'Jose da Silva',
                'email' => 'josedasilva@gmail.com',
                'order_id' => 1,
                'soft_descriptor_light' => substr(preg_replace('/\s/', "", sanitize(env('APP_NAME'))), 0, 10)
            ]
        ];

        $expires_at = date("Y-m-d H:i:s", strtotime(today() . " + 1 month"));

        $result = IuguChargeQueue::push(
            new IuguChargeQueueDataList([
                'token' => [
                    'verb' => 'POST',
                    'uri' => '/payment_token',
                    'headers' => $headers,
                    'query_string' => null,
                    'payload' => $payload_token
                ],
                'charge' => [
                    'verb' => 'POST',
                    'uri' => '/charge?api_token=' . env('IUGU_API_TOKEN'),
                    'headers' => $headers,
                    'query_string' => null,
                    'payload' => $payload_charge
                ],
                'meta' => [
                    'order_id' => $order->id,
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoice->id
                ]
            ]), 
            $expires_at
        );

        debug_html($result);
    }
    
    public function iugu_charge__queue_send(Request $request, $id)
    {
        $entity = ModelsIuguChargeQueue::find($id);
        IuguChargeQueue::send(new IuguChargeType([
            "data" => $entity->data,
            "entity" => ModelsIuguChargeQueue::find($id)
        ]));
    }

    public function test_123()
    {
        echo ModelsSmtp::first()->id ?? '';
    }

    public function phpinfo(Request $request)
    {
        // if ($request->query('pw') === '0x000001e9') phpinfo();
    }

    public function seller_credit_queue(Request $request)
    {
        // adiciona na fila
        $entity = SellerCreditQueue::push(
            json_encode([
                'user_id' => 184,
                'amount' => 500
            ]),
            date('Y-m-d H:i:s', strtotime(today() . ' + 14 days'))
        );

        // executa item
        $result = SellerCreditQueue::send(new SellerCreditType([
            "data" => $entity->data,
            "entity" => $entity
        ]));

        // debug_html($result);
    }

    public function seller_credit_risk()
    {
        echo SellerBalance::chargebackPercent(184);
    }

    public function seller_credit_remove_queue_item()
    {
        SellerCreditQueue::removeWhereData(
            new SellerCreditBodyWhere([
                'order_id' => 1953
                // 'amount' => 500
            ]),
            ESellerCreditQueueStatus::WAITING
        );
    }

    public function seller_credit_update_items_queue()
    {
        SellerCreditQueue::updateWhere(
            new SellerQueueUpdateWhere([
                'data' => new SellerCreditBodyWhere([
                    'order_id' => 1957
                ]),
                'status' => ESellerCreditQueueStatus::WAITING
            ]),
            new SellerQueueUpdateData([
                'status' => ESellerCreditQueueStatus::CANCELED,
            ])
        );

        SellerCreditQueue::updateWhere(
            new SellerQueueUpdateWhere([
                'data' => new SellerCreditBodyWhere([
                    'order_id' => 1957,
                    'amount' => 0.4
                ]),
                'status' => ESellerCreditQueueStatus::CANCELED
            ]),
            new SellerQueueUpdateData([
                'status' => ESellerCreditQueueStatus::WAITING,
            ])
        );
    }
    public function onesignal()
    {
        // try 
        // {
        /**
         * Notificação push com OneSignal
         */

        $amounts = [27, 29.9, 35, 27, 27];

        for ($i = 0; $i <= 5; $i++)
        {
            foreach ($amounts as $amount)
            {
                $onesignal = new OneSignal;
                $onesignal->setTitle("Venda realizada!");
                $onesignal->setDescription("Sua Comissão: $ " . number_format($amount, 2));
                $onesignal->addExternalUserID('felipezilz@hotmail.com');
                $response = $onesignal->pushNotification();
            }
        }

        // }

        // catch (Exception)
        // {
        //     // TODO: adicionar erro a lista de erros
        // }

        //

    }

    public function resend_webhooks_charges()
    {
        // objetivo
        // atualizar as linhas de webhook_queues com meta->order_id = uuid
        //
        $orders = [
            ['ced7120e-912b-4d8e-bd32-921fc90f808d', 2990, 'domaalma@gmail.com'],
            ['55d29b7f-5cca-454c-9357-06ea8c59bbf7', 3500, 'domaalma@gmail.com'],
            ['19cbd996-1e83-484c-9001-2ce46817b748', 2990, 'martinezroberto3320@gmail.com'],
            ['facb3113-0d29-4f2a-bc8e-446d0530c2ec', 3500, 'martinezroberto3320@gmail.com'],
            ['feafaa23-bd71-4467-bec5-338e773b0d25', 2990, 'carlosnunez023@gmail.com'],
            ['c1e770d3-e8bd-4c6e-a8bc-4f94884ecfe4', 3500, 'carlosnunez023@gmail.com'],
            ['38035b49-78e6-42fc-aaa4-edfd501c943f', 2990, 'lulu74mgarcia@gmail.com'],
            ['a022c0e1-0111-48da-90bb-187b7d80ef25', 3500, 'lulu74mgarcia@gmail.com'],
            ['6ab572f9-727d-4153-a631-cf5faa1c96c8', 2990, 'hernandezmichel1974@gmail.com'],
            ['a179558c-3014-47e0-84f8-e39179f50265', 3500, 'hernandezmichel1974@gmail.com'],
            ['66b85db9-ac26-4866-9296-fa0626fd73de', 2990, 'aycheltorresp19@hotmail.com'],
            ['0aafb4f6-60e0-4df5-82d2-fee6a4e27316', 3500, 'aycheltorresp19@hotmail.com'],
            ['8f93203f-6200-4064-bb3c-0ed2b4379ce4', 2990, 'sofiasilerio4@yahoo.com'],
            ['4bf83738-6ce8-4306-b71f-d1fb3fc7090d', 3500, 'sofiasilerio4@yahoo.com'],
            ['111bd8e1-c217-42ec-ae81-e3b75c6f7156', 2990, 'aracellyforero@gmail.com'],
            ['f49ab506-12c0-4132-a314-37652c62f3a0', 3500, 'aracellyforero@gmail.com'],
            ['ca6151d4-5ef4-4ead-9206-c19ef66b5d7f', 2990, 'scastrom_7@hotmail.com'],
            ['6be2446a-e5a0-4216-84cc-9e7a439e8425', 3500, 'scastrom_7@hotmail.com'],
            ['b31449f1-192c-4f42-b2c5-0065ab9670ff', 2990, 'zulimasykes@gmail.com'],
            ['2fabff39-efc2-462e-aa69-8caf91007b50', 2990, 'malenarivera_piza@hotmail.com'],
            ['d615e671-5c54-4427-a1c4-3880d7f10b6b', 3500, 'malenarivera_piza@hotmail.com'],
            ['51b14ae9-25f4-43db-bba5-01f610fa201e', 2990, 'huntvaldesn2@gmail.com'],
            ['43b68b95-f3b4-43d1-ae2a-5fd872e6b07b', 3500, 'huntvaldesn2@gmail.com'],
            ['27b35e24-43da-412a-bfac-b7439756336e', 2990, 'lettyhrea56@hotmail.com'],
            ['1aab32b3-d6b9-4810-9ccc-1ce1b34fec28', 3500, 'lettyhrea56@hotmail.com'],
            ['50ed9177-3719-4834-a679-2d391c78d7b9', 2990, 'hectorjlinares@hotmail.com'],
            ['e0508c64-5d0d-4537-bd15-ea58e5b1121e', 2990, 'mendoza.laurencia@yahoo.com'],
            ['a0fc0382-b32c-4823-ae10-b82a7e6f76b4', 2990, 'taxidi1@hotmail.com'],
            ['046e7eda-9fce-43ab-8ff9-57b1177221e7', 3500, 'taxidi1@hotmail.com'],
            ['a06e0d0d-3c7d-497c-8ab7-02bf24e36517', 2990, 'mendoza.laurencia@yahoo.com'],
            ['c95bbce1-6a0a-43c8-86a6-00536ecbd486', 2990, 'mrnf0570@gmail.com'],
            ['303faa02-38b3-4e2a-ade8-0792bc9d4866', 3500, 'mrnf0570@gmail.com'],
            ['ee236685-5a7a-420c-a410-b06b7bfebd53', 2990, 'daniel_larios73@hotmail.com'],
            ['3f534557-5722-49d7-9eeb-a4af2f951a53', 3500, 'daniel_larios73@hotmail.com'],
            ['643122f4-4135-4c7c-a394-8aa9c5ab27c5', 2990, 'dpacas77@hotmail.com'],
            ['bd6d9df6-ef55-4ac3-bc86-1b22b4ae95bd', 3500, 'dpacas77@hotmail.com'],
            ['e2bcec15-b930-41a7-8528-f178c9443f77', 2990, 'ale_cash@yahoo.com.mx'],
            ['f6066a81-da34-4594-95fe-fcc9fdf5a662', 3500, 'ale_cash@yahoo.com.mx'],
            ['e112b635-0d84-4dbe-b95d-6fd9711b6e29', 2990, 'reinae95@gmail.com'],
            ['cf4d5c2a-7575-4121-9b17-2750a78eeadc', 3500, 'reinae95@gmail.com'],
            ['340afc30-2554-453c-89db-2c9696a47266', 2990, 'zunino107@gmail.com'],
            ['4196c159-b627-4dd5-9509-d74a2ba0b600', 3500, 'zunino107@gmail.com'],
            ['a48e9e61-82e5-4012-a12d-a7de1f8528dd', 2990, 'eduardo_alanis58@hotmail.com'],
            ['aa71018d-e061-4156-a20c-cb305d166b1f', 2990, 'miguelespram@gmail.com'],
            ['d4839dbc-25c0-4619-984c-d0e1a674236c', 3500, 'miguelespram@gmail.com'],
            ['f1aec392-ce0e-4294-ac1f-5229b3f2d9d6', 2990, 'luzrestrepo4@gmail.com'],
            ['bcf797a0-8b9e-46b6-b14c-b03213e9aacc', 3500, 'luzrestrepo4@gmail.com'],
            ['b1be2c9c-15f8-411d-94dd-a87122198068', 2990, 'r.chinchilla96@gmail.com'],
            ['b484fc13-b730-44f9-8390-c4115c0f00d1', 3500, 'r.chinchilla96@gmail.com'],
            ['7d6a2add-93c8-4953-b046-f20358523049', 2990, 'celiaaguayo17@gmail.com'],
            ['226997b3-9b4c-4a17-9920-d552a3ff3206', 3500, 'celiaaguayo17@gmail.com'],
            ['d94e41d0-1184-4965-97a2-5285435d1874', 2990, 'madreselva1956@gmail.com'],
            ['a68ef4f0-9cb8-4472-a912-be9f877e7886', 3500, 'madreselva1956@gmail.com'],
            ['45a4a517-3000-4dc1-bd26-0ecca7d7b020', 2990, 'boxnic@yahoo.com'],
            ['076d31c6-e091-4518-bfb9-767abb5e8a3d', 3500, 'boxnic@yahoo.com'],
            ['c65ac5ed-4760-4907-add0-c6bef58e63d3', 2990, 'papa.sympho@gmail.com'],
            ['8730d5f0-9f22-4bb0-9272-21dcfe29bb22', 2990, 'catalina.gudino@gmail.com'],
            ['4c40b548-7dc2-4d73-b8f3-677266854d29', 3500, 'catalina.gudino@gmail.com'],
            ['57234e53-e319-4848-9026-f7dc90b1395f', 2990, 'buche1971@gmail.com'],
            ['aeda061f-b528-4ab4-a476-74de47ba5bb1', 3500, 'buche1971@gmail.com'],
            ['d078fc5c-8665-4c7f-9bb6-877b944e4e8b', 2990, 'Susyruiz08@gmail.com'],
            ['01e6bb3a-9a73-4e3b-b04d-92d6912e47be', 3500, 'Susyruiz08@gmail.com'],
            ['847cc763-f249-4e89-80ed-c170a0885c87', 2990, 'robertogalindo404@gmail.com'],
            ['0600044d-b472-4538-afe8-386d91eb5a61', 3500, 'robertogalindo404@gmail.com'],
            ['36e50537-9e60-40c3-93b0-eb6e02147d9b', 2990, 'Efrainferrer74@hotmail.com'],
            ['efc13eb9-a1d5-4ff2-b9f0-7d176d787da0', 3500, 'Efrainferrer74@hotmail.com'],
            ['c9579f37-16b0-4b0a-b391-dc3034866e91', 2990, 'Efrainferrer74@hotmail.com'],
            ['0202210c-8be4-4b5d-8ca3-aabf6a05baf9', 3500, 'Efrainferrer74@hotmail.com'],
            ['ff166186-feea-4fc4-81f9-de58895f2722', 2990, 'Therrymedrano@yahoo.com'],
            ['ca9accca-48d2-43d5-b9cd-81e7fb34c1eb', 2900, 'gerencia@deliciascuccine.com.py'],
            ['5ab14aed-55bd-408b-8628-48d16ec95da7', 2500, 'gerencia@deliciascuccine.com.py'],
            ['68dc10e2-113f-40e5-b4ab-f7adecc6c1e1', 2990, 'jesusmnieves@aol.com'],
            ['d4e5d007-e6c6-4918-b398-216d408bbec0', 3500, 'jesusmnieves@aol.com'],
            ['ff2a5b25-f22d-45ec-950f-9eeddd50eef8', 2990, 'llamadadedios7@aol.com'],
            ['049e303e-8123-4ea0-ac52-06c1a2c3f755', 3500, 'llamadadedios7@aol.com'],
            ['9c054aab-af13-4c7b-a502-833b1389b2c4', 2990, 'marioquintanar1014@icloud.com'],
            ['2bc5b0ff-7e02-450d-a2bc-3937f0332f1e', 3500, 'marioquintanar1014@icloud.com'],
            ['792ffd37-248a-4363-9fab-8d4178796c4d', 2990, 'bcachae@gmail.com'],
            ['8db73956-f241-4909-ae67-c46e0a1f412c', 3500, 'bcachae@gmail.com'],
            ['2bd1b2ad-9a43-4cbb-a262-80fe891f0cad', 2990, 'luiscolores_6@hotmail.com'],
            ['93c25e81-eb29-44bb-b3e1-7f0c5ca9cdd9', 3500, 'luiscolores_6@hotmail.com'],
            ['ea783bc1-e56f-42ed-98d6-653e7cc6a361', 2990, 'apolina36@gmail.com'],
            ['630ed084-6424-4df6-b6f0-b31d5559ba58', 3500, 'apolina36@gmail.com'],
            ['5000adb7-c934-4589-bc8e-7927a574836a', 2900, 'eporrasr@hotmail.com'],
            ['99d60931-61e1-459f-8c89-6c491a62f6c2', 2500, 'eporrasr@hotmail.com'],
            ['960cd180-3729-489f-adfb-99bf56b0e127', 4900, 'eporrasr@hotmail.com'],
            ['17661b48-8099-432a-ada6-da4948bcbcef', 2990, 'jesusmtzcasper@gmail.com'],
            ['5cff85e0-a57d-4998-a9a9-0abbbe0b0bdd', 3500, 'jesusmtzcasper@gmail.com'],
            ['09ed82ee-5b15-444c-9b1f-023e9a093c6e', 2990, 'casarogui_69@live.com'],
            ['166edd95-855e-48d8-959a-3d3183187b2f', 2990, 'catalinavelazquez78@gmail.com'],
            ['b78f5b8c-916c-43d3-84b0-a8b39e811790', 3500, 'catalinavelazquez78@gmail.com'],
            ['ad9187df-a0a0-4255-a0f2-6dbd20fcc32f', 2990, 'regisloya_o@hotmail.com'],
            ['33ae78ca-1fe3-4d02-9834-07ecd331097e', 3500, 'regisloya_o@hotmail.com'],
            ['c2301b6e-c563-4655-8c7f-4bfa4c720ccf', 2990, 'cesarbelizarioutrilla@gmail.com'],
            ['22b11a8a-5227-4010-93bd-6d215a946b10', 3500, 'cesarbelizarioutrilla@gmail.com'],
            ['cfc8b3d4-9954-4c41-ab49-95e88e1569b2', 2990, 'osvelia30@hotmail.com'],
            ['df871764-7ed2-442d-ba09-1578cd20101e', 3500, 'osvelia30@hotmail.com'],
            ['a616646d-68de-4a10-8e2c-d65d17be8521', 2990, 'solisemi@hotmail.com'],
            ['dd4c81ed-15db-4d98-8faa-e349ad94ca70', 3500, 'solisemi@hotmail.com'],
            ['c88f833b-72a8-4ab6-83cb-7932d83714af', 2990, 'tugabones@gmail.com'],
            ['032e2b85-4c9d-48da-b04c-30840b12eacd', 2990, 'Luckyfersi@yahoo.com'],
            ['c1b8e74d-3dc5-44e0-9e4b-22b8bed8a736', 3500, 'Luckyfersi@yahoo.com'],
            ['12d034b0-c7c6-46bf-8342-eb9872bd14b7', 2990, 'Luckyfersi@yahoo.com'],
            ['3bf093ba-3f44-4b89-b820-96597ab06955', 3500, 'Luckyfersi@yahoo.com'],
            ['bf97fb7a-d00d-40cf-9a4b-8137a71b233c', 2990, 'Luckyfersi@yahoo.com'],
            ['19706788-6762-47a2-933e-acf8a004fc02', 3500, 'Luckyfersi@yahoo.com'],
            ['eec9a821-8002-411c-9748-53bac52a4f31', 2990, 'roxanaroman2007@yahoo.com'],
            ['24c8ef6f-c26b-411a-9e37-0ff1aaf3e3d4', 3500, 'roxanaroman2007@yahoo.com'],
            ['a3a0bd25-1eb8-43b0-96cf-38fa109618cd', 2990, 'clauditadelgado70@gmail.com'],
            ['17c71b26-6213-405f-b9e9-3d6c11f4e601', 3500, 'clauditadelgado70@gmail.com'],
            ['b6009b95-b6f0-441e-90dd-03a5c691bf07', 2990, 'lgsl63@hotmail.com'],
            ['36b3982d-2cbc-413a-8672-c0142a77b604', 3500, 'lgsl63@hotmail.com'],
            ['747cb721-937d-48ec-9869-04597bbd0397', 2990, 'fatimag,@gmail.com'],
            ['321df7b1-a31f-4e0c-9f2a-7c73087e4c40', 3500, 'fatimag,@gmail.com'],
            ['a702ed8c-6cf0-4e82-87b3-e9a574095609', 2990, 'nerto38@gmail.com'],
            ['96823d59-1f26-40f3-a465-f4fa5d7e3def', 3500, 'nerto38@gmail.com'],
            ['63009f19-46e0-4121-9a46-7a597a340fab', 2990, 'Wwwrosamaciel88@yahoo.com'],
            ['137e8040-6168-49e5-a372-69d649d15c74', 2990, 'Almalu2000@yahoo.com'],
            ['ed5fab32-33c0-4de9-86ad-d19feee10ca6', 3500, 'Almalu2000@yahoo.com'],
            ['777dbb6b-4f53-4678-b3ac-8329cad3e913', 2990, 'gr878794@gmail.com'],
            ['4e6ca2a6-3210-4f4d-b2a9-3bb56f629a1a', 3500, 'gr878794@gmail.com'],
            ['0e735e4c-02cd-44b2-af1a-73997181663f', 2990, 'lilianacastrofer1@gmail.com'],
            ['4de5d71b-de1a-4a5b-8490-ced14ea0ad03', 2990, 'santanagd@hotmail.com'],
            ['95810f54-fe7e-49e2-b164-35a86165a11e', 3500, 'santanagd@hotmail.com'],
            ['f1641382-7c0a-4bbb-aece-679acc0bda60', 2990, 'lsnava@prodigy.net.mx'],
            ['19c02765-231b-41cf-a446-5ef15b27f43e', 2990, 'villami_5307@hotmail.com'],
            ['c721350a-4102-43a9-8554-d00faa8c2765', 3500, 'villami_5307@hotmail.com'],
            ['806c8748-d79c-47a7-aa56-c51e3dc5dcde', 2990, 'cbmolkas@yahoo.com.mx'],
            ['9c86684e-8ea4-4f7b-957d-fa0daa7b16c4', 3500, 'cbmolkas@yahoo.com.mx'],
            ['f4e39e95-1629-49f1-87ff-04516aa65686', 2990, 'gladiola80@hotmail.com'],
            ['bdd8c6a6-7fcf-4275-abdf-d197e22ce54f', 3500, 'gladiola80@hotmail.com'],
            ['09e328ca-cc29-4b42-9ca6-7a029fd1a89e', 2990, 'estuardo976@gmail.com'],
            ['4a386ea7-7bb1-49dc-948b-91a9a769f3bf', 3500, 'estuardo976@gmail.com'],
            ['16d12040-be61-4461-9deb-3e3e88eeb14d', 2990, 'jorgevaldiviavazquez@hotmail.com'],
            ['1f670dfe-c5ed-4e38-b787-6ca9f3c13771', 3500, 'jorgevaldiviavazquez@hotmail.com'],
            ['de0625b6-d8b3-435a-a40a-596747ddf18b', 2990, 'marthatanaca1775@gmail.com'],
            ['97932b2a-211d-4378-b7a5-a7d89117cd3d', 3500, 'marthatanaca1775@gmail.com'],
            ['2866f5d4-c03a-4ea6-9b12-71a7013ec32c', 2990, 'ges_44@hotmail.com'],
            ['9cd5776f-5c0d-4c40-8d36-1219c9f5654f', 2990, 'ges_44@hotmail.com'],
            ['eba57c9c-c716-4611-bda6-dab579378bd1', 3500, 'ges_44@hotmail.com'],
            ['f14bcc80-8244-42ae-9dd7-c1c4b03abbe9', 2990, 'Sosa8899@sbcglobal.net'],
            ['10fc67a7-a6df-4412-b13d-81f557032cd5', 2990, 'Sosa8899@sbcglobal.net'],
            ['2cbc701f-98a4-4306-96fd-604761617a9b', 2990, 't.essono@gmail.com'],
            ['8d0c9c29-56d6-4c41-8c58-e3a2af1c8a88', 2990, 'chemysbat@gmail.com'],
            ['cc3a131f-a3af-4dbc-94d2-3b9524630272', 3500, 'chemysbat@gmail.com'],
            ['e33ddb6d-dbfa-4494-9316-5b54ddeeced8', 2990, 'autoaccesoriosfama@yahoo.com.mx'],
            ['c23e1a5d-c1d4-4bf9-ab32-e634595f1880', 2990, 'carmenr324@sbcglobal.net'],
            ['1dec650b-9218-43ee-b9c2-c9188837988e', 3500, 'carmenr324@sbcglobal.net'],
            ['3e10bd80-d1b6-429b-8b26-f47d890af1dd', 2990, 'misslorena4@hotmail.com'],
            ['5c0cb04e-21dd-4b70-9484-5dae708e16f9', 3500, 'misslorena4@hotmail.com'],
            ['41f933c5-94f0-4d52-a373-561d1d65838e', 2990, 'ppaltv57@gmail.com'],
            ['8d5e6c15-db7d-4382-93ad-415e5c2ab02c', 3500, 'ppaltv57@gmail.com'],
            ['b90e3780-e5ea-464d-8b29-cc02a92589d6', 2990, 'ecastaneda2808@yahoo.com.mx'],
            ['7c8dd23a-6a1e-4915-9e26-a15a0d1d4905', 3500, 'ecastaneda2808@yahoo.com.mx'],
            ['bd3ab5e5-4bdc-407d-b34b-c263e554c93a', 2990, 'shattenmen@gmail.com'],
            ['2bfe8b9d-b488-462a-8213-65c850463e4b', 3500, 'shattenmen@gmail.com'],
            ['34c1bf85-f892-4554-986f-231836397858', 2990, 'negretealicia61@gmail.com'],
            ['4617fe80-e329-4504-949b-3fd0a0bd4a17', 3500, 'negretealicia61@gmail.com'],
            ['5d131124-f5f3-462e-ab1a-1c66414999a6', 2990, 'jorgerodrigoo2019@gmail.com'],
            ['1b553eca-4534-41de-810f-bc99187005e0', 3500, 'jorgerodrigoo2019@gmail.com'],
            ['fd50e7fe-39bc-4d06-a12e-1bbf240a3b3a', 2990, 'Reynacanongo72@gmail.com'],
            ['63bacec5-24d1-4d5a-83c4-e3123ce8facd', 3500, 'Reynacanongo72@gmail.com'],
            ['79f3944e-7251-4a73-902e-02f2210ca209', 2990, 'nrubio33196@yahoo.com'],
            ['6539cf6d-322c-4e7c-a6e2-9d452dcd1b6b', 3500, 'nrubio33196@yahoo.com'],
            ['08ec556c-f031-4b32-977f-0a26c5d6ffbb', 2990, 'kikeking1720@iclou.com'],
            ['c4ef7e5a-97b6-4af0-972e-00f0c47fc0ba', 3500, 'kikeking1720@iclou.com'],
            ['285c5d09-f0ce-4a61-80e5-ecec0bd133fe', 2990, 'alsa7540@aol.com'],
            ['3050ac18-6865-417e-8866-331fc4a6fc03', 3500, 'alsa7540@aol.com'],
            ['24df6c01-f518-4039-b558-111870d4cd47', 2990, 'mary_cave@hotmail.com'],
            ['7a72eb9c-3f49-476c-b200-dd3d10720aa4', 3500, 'mary_cave@hotmail.com'],
            ['a01c7eb8-f188-4828-86f8-5c97f35f659a', 2990, 'reinarod13@yahoo.com'],
            ['c545a5ba-47ef-41cf-93b5-9498b548fbbd', 3500, 'reinarod13@yahoo.com'],
            ['eb6c4482-4350-4e8f-b318-ebe81c68b6c4', 2990, 'hernandezconstructionllc47@gmail.com'],
            ['0f0ac4c1-ed29-4266-af7a-baa40202de35', 3500, 'hernandezconstructionllc47@gmail.com'],
            ['d0685771-b451-426d-8d4f-c34b509f408e', 2990, 'Marcelosantosgarcia1975@gmail.com.mx'],
            ['8765bea9-83b9-4bcd-ac40-76202087ebb3', 3500, 'Marcelosantosgarcia1975@gmail.com.mx'],
            ['304c3c07-8f47-4e3c-b8f1-b056a379e948', 2990, 'carlosunda@hotmail.es'],
            ['3f6d2b9b-867c-4732-8500-cc8e9cd49689', 3500, 'carlosunda@hotmail.es'],
            ['885117f8-8d9b-413a-b6db-14ccb2d982d8', 2990, 'nestorcardoso26@gmail.com'],
            ['7e15ab6a-6b33-4d9a-86fa-76572c4bdc91', 2990, 'cristobiene36@gmail.com'],
            ['0d59ceb9-64cb-4743-9ca2-851bd92e158b', 3500, 'cristobiene36@gmail.com'],
            ['9d6084f5-e61b-41e5-8177-fa89b04624dd', 2990, 'cristobiene36@gmail.com'],
            ['15859922-5a7f-4745-97b5-959d5e10f1a1', 3500, 'cristobiene36@gmail.com'],
            ['2ca445c1-6162-4a7a-949c-671228dd68a5', 2990, 'Virgenromero@hotmail.com'],
            ['3c8683da-12c8-48b5-bf44-9c43e5ca1689', 3500, 'Virgenromero@hotmail.com'],
            ['b29f8d34-b757-4b3b-8b1c-fbdec54d4d79', 2990, 'bochoathayde@gmail.co'],
            ['7416970d-98a4-4a7b-a13d-5a5f1eb6f6a3', 3500, 'bochoathayde@gmail.co'],
            ['d52541a0-3b81-4990-bd02-e94daea5b1cd', 2990, 'lmmtopobay@gmail.com'],
            ['9e485502-3d79-4ac2-af03-fb53eeeea873', 3500, 'lmmtopobay@gmail.com'],
            ['e73f62e3-a298-46ce-ab56-cc21597dba7e', 2990, 'mgprf24@live.com.mx'],
            ['046ed8e9-8dab-4eaf-9de2-43c93b72c1b6', 3500, 'mgprf24@live.com.mx'],
            ['72a23f1a-cb62-4a5a-90a1-3bb14208714d', 2990, 'ayutell1@gmail.com'],
            ['da859669-7781-4312-ab0d-a8d9ed1d4ead', 3500, 'ayutell1@gmail.com'],
            ['900f0dff-16fe-43aa-8ee7-beead9bfece4', 2990, 'elsaperez90@yahoo.com'],
            ['35537a82-f204-4917-9b2f-443cb662efe5', 2990, 'evamconde@hotmail.com'],
            ['f4edb1c4-4c2a-45f4-8cbd-473a887be0cf', 3500, 'evamconde@hotmail.com'],
            ['01471d65-a891-4ecb-91b1-e28921e90417', 2990, 'lilakw2004@yahoo.com'],
            ['7d440304-0dd3-449f-a5e8-b10fa2ced09d', 3500, 'lilakw2004@yahoo.com'],
            ['979c5335-4758-415f-a073-8a74637b9c8a', 2990, 'jipi300@hotmail.com'],
            ['96364e51-7747-4efc-9231-15dece9a45eb', 3500, 'jipi300@hotmail.com'],
            ['f1826424-4dce-4722-ad00-2536253102b0', 2990, 'rvolta@gmail.com'],
            ['b3d48280-7457-45f9-82ae-8b96c292ecdd', 2990, 'lucilaabbey0714@gmail.com'],
            ['bd42d2c5-7f5b-4736-a77d-d12fccb8e97a', 3500, 'lucilaabbey0714@gmail.com'],
            ['09a15b1f-badd-4c9e-ae52-d68f4d44648f', 2990, 'maria.p.mp905@gmail.com'],
            ['b7bdc806-4a6b-4367-bba0-5ff1d7dc9509', 2990, 'dulce5069@icloud.com'],
            ['cc5dfa88-7a78-4abb-84ba-52816ffcb7f5', 3500, 'dulce5069@icloud.com'],
            ['bae4e63c-29e7-46bb-8841-d8974a8de4cc', 2990, 'pedrozelaya0315@gmail.com'],
            ['e023c97d-e4bb-431e-ad48-1332a5eebf32', 2990, 'hector.rivera09888@gmail.com'],
            ['ea6dcfd5-e591-4563-85f3-a5804d16d901', 3500, 'hector.rivera09888@gmail.com'],
            ['e7dff680-067c-4cfa-bfdb-fd6862cf2006', 2990, 'Martharox33@gmail.com'],
            ['ed1cc35a-4761-4fb1-ae2c-ce6dea51967e', 3500, 'Martharox33@gmail.com'],
            ['6e9275ad-39a3-4b45-9340-473aa4e74c40', 2990, 'Victorfarrera2017@gmail.com'],
            ['09476b57-1b4a-415a-b9ea-d73268731b32', 3500, 'Victorfarrera2017@gmail.com'],
            ['5e75dbf7-0b29-409b-a0b1-727e0eb775dd', 2990, 'Otono_7777@hotmail.com'],
            ['6e4069df-658f-430f-a6d0-b5db4af4544a', 3500, 'Otono_7777@hotmail.com'],
            ['7422ed0f-8df0-4fd5-9009-3a6aca8b3d2d', 2990, 'mariaelenamurillorojo@gmail.com'],
            ['235c0325-f8b6-4c59-aeb1-7dc976bdd8c7', 3500, 'mariaelenamurillorojo@gmail.com'],
            ['87b2e311-7a8d-47f6-bb39-8caf6d7d9d8f', 2990, 'hduranl@outlook.com4421183193'],
            ['5ed1f9f2-a2b7-4f01-ab43-a9dfb30e2a83', 3500, 'hduranl@outlook.com4421183193'],
            ['c3e1613a-ef49-4879-9304-79bf46f0900b', 2900, 'lourdes77schmitz@gmail.com'],
            ['16c64e81-27f2-425d-98de-dd7c44c75b5d', 2900, 'lourdes77schmitz@gmail.com'],
            ['0ec0cbb1-ec15-49ea-abaa-c14d4db7e1fd', 2900, 'lourdes77schmitz@gmail.com'],
            ['3c27e2bc-da61-4681-aa06-92d11cbb0b70', 2990, 'Ivonne2807@hotmail.com'],
            ['ffa5aa0e-6833-43a2-aac2-0ee7f60c7700', 3500, 'Ivonne2807@hotmail.com'],
            ['050b50c4-c701-4538-8f87-fcc63cd68d7a', 2990, 'Ivonne2807@hotmail.com'],
            ['8fb48510-875b-4e32-b801-20cf8e6bee6d', 3500, 'Ivonne2807@hotmail.com'],
            ['537a023d-5191-4ddb-affc-501bab15976d', 2990, 'reinasava@hotmail.com'],
            ['f5d5517d-27b5-4ccf-8ff8-11167c6ff7e1', 3500, 'reinasava@hotmail.com'],
        ];

        $wooks = [
            ['2990', 'martinezroberto3320@gmail.com', 'pi_3PJqtn04Gx7pql3l1t1mWCYI'],
            ['3500', 'martinezroberto3320@gmail.com', 'pi_3PJqu104Gx7pql3l0ZireU9x'],
            ['2990', 'carleduvale@gmail.com', 'pi_3PJFZp04Gx7pql3l0PsRDyqj'],
            ['2990', 'fabroalba@gmail.com', 'pi_3PItRT04Gx7pql3l0VTj1hGn'],
            ['2990', 'xhec77@hotmail.com', 'pi_3PJGPr04Gx7pql3l0xCMLZW8'],
            ['3500', 'carlosnunez023@gmail.com', 'pi_3PJrTo04Gx7pql3l0JkNFBFA'],
            ['2990', 'carlosnunez023@gmail.com', 'pi_3PJrTM04Gx7pql3l1Q0OOip5'],
            ['2990', 'lulu74mgarcia@gmail.com', 'pi_3PJrfK04Gx7pql3l0HR646WI'],
            ['3500', 'lulu74mgarcia@gmail.com', 'pi_3PJrg204Gx7pql3l1fC7m49V'],
            ['2990', 'ycortes5512@gmail.com', 'pi_3PIw6X04Gx7pql3l0eFHFgRx'],
            ['2990', 'ycortes5512@gmail.com', 'pi_3PIwBy04Gx7pql3l07STjOg2'],
            ['2990', 'thezunigasj@gmail.com', 'pi_3PJJjV04Gx7pql3l0AIiGk4V'],
            ['2990', 'thezunigasj@gmail.com', 'pi_3PJJg104Gx7pql3l1kgDyHEs'],
            ['2990', 'angie4411@hotmail.com', 'pi_3PJMgP04Gx7pql3l04CUTuN5'],
            ['2990', 'cristina_e07@yahoo.com', 'pi_3PJ1i304Gx7pql3l0vwKtop6'],
            ['2990', 'lmaravil@tec.mx', 'pi_3PJ2Xq04Gx7pql3l1SjC4I4Y'],
            ['2990', 'domaalma@gmail.com', 'pi_3PJp8u04Gx7pql3l02DiAkcx'],
            ['3500', 'domaalma@gmail.com', 'pi_3PJp9B04Gx7pql3l0D4uGS3E'],
            ['2990', 'hernandezmichel1974@gmail.com', 'pi_3PK9aG04Gx7pql3l1UXO7KFU'],
            ['2990', 'aycheltorresp19@hotmail.com', 'pi_3PK9lo04Gx7pql3l1bmWjx3N'],
            ['2990', 'sofiasilerio4@yahoo.com', 'pi_3PK9oY04Gx7pql3l0xpuPQ5O'],
            ['3500', 'sofiasilerio4@yahoo.com', 'pi_3PK9ow04Gx7pql3l158CyPAl'],
            ['2990', 'tina.diran58@icloud.com', 'pi_3PJ7KN04Gx7pql3l1Sw8sio0'],
            ['2990', 'lcp.enrique.esquivel@gmail.com', 'pi_3PJ7Cs04Gx7pql3l1g7xKl21'],
            ['2990', 'martingonzalezarchundiadia63@gmail.com', 'pi_3PJ7di04Gx7pql3l1OvePNKO'],
            ['2990', 'aracellyforero@gmail.com', 'pi_3PKAdC04Gx7pql3l1Ai4xGCE'],
            ['3500', 'aracellyforero@gmail.com', 'pi_3PKAda04Gx7pql3l07ZW87Y2'],
            ['2990', 'scastrom_7@hotmail.com', 'pi_3PKAfO04Gx7pql3l0QNWad5l'],
            ['3500', 'scastrom_7@hotmail.com', 'pi_3PKAfc04Gx7pql3l1imQ5rpP'],
            ['2990', 'Davidzaz13@hotmail.com', 'pi_3PJDYh04Gx7pql3l1H4WHMFx'],
            ['2990', 'Davidzaz13@hotmail.com', 'pi_3PJDY904Gx7pql3l0uO5TkzA'],
            ['2990', 'zulimasykes@gmail.com', 'pi_3PKG6Z04Gx7pql3l17BeYxEZ'],
            ['2990', 'Davidzaz13@hotmail.com', 'pi_3PJDZs04Gx7pql3l01aAKaQH'],
            ['2990', 'Davidzaz13@hotmail.com', 'pi_3PJDZ404Gx7pql3l11lZteSz'],
            ['2990', 'Davidzaz13@hotmail.com', 'pi_3PJDXh04Gx7pql3l0iCA3ZMf'],
            ['2990', 'malenarivera_piza@hotmail.com', 'pi_3PKGYT04Gx7pql3l07R9sGoy'],
            ['3500', 'malenarivera_piza@hotmail.com', 'pi_3PKGYh04Gx7pql3l1I18U4fy'],
            ['2990', 'jcrogv@gmail.com', 'pi_3PKHBz04Gx7pql3l0suLnq1J'],
            ['3500', 'jcrogv@gmail.com', 'pi_3PKHCE04Gx7pql3l1tImI6Uk'],
            ['2990', 'huntvaldesn2@gmail.com', 'pi_3PKI9S04Gx7pql3l1ZDgQ676'],
            ['3500', 'huntvaldesn2@gmail.com', 'pi_3PKI9i04Gx7pql3l1KZRAFRM'],
            ['2990', 'lettyhrea56@hotmail.com', 'pi_3PKIrD04Gx7pql3l0bgH6m4F'],
            ['2990', 'hectorjlinares@hotmail.com', 'pi_3PKKvs04Gx7pql3l0RQGgZNu'],
            ['2990', 'mendoza.laurencia@yahoo.com', 'pi_3PKLm004Gx7pql3l0wdIpuvl'],
            ['2990', 'taxidi1@hotmail.com', 'pi_3PKLmV04Gx7pql3l0SG4DyQ7'],
            ['3500', 'taxidi1@hotmail.com', 'pi_3PKLmc04Gx7pql3l0xtz22Yo'],
            ['2990', 'mrnf0570@gmail.com', 'pi_3PKLwi04Gx7pql3l1bEM7o2i'],
            ['3500', 'mrnf0570@gmail.com', 'pi_3PKLww04Gx7pql3l1VjStMaM'],
            ['2990', 'daniel_larios73@hotmail.com', 'pi_3PKM9c04Gx7pql3l0j4V4Hes'],
            ['3500', 'daniel_larios73@hotmail.com', 'pi_3PKM9l04Gx7pql3l0DMuA9xy'],
            ['2990', 'dpacas77@hotmail.com', 'pi_3PKMRT04Gx7pql3l0TR7VfBk'],
            ['3500', 'dpacas77@hotmail.com', 'pi_3PKMRp04Gx7pql3l1NtG0iwi'],
            ['2990', 'ale_cash@yahoo.com.mx', 'pi_3PKNvS04Gx7pql3l0b6GiebO'],
            ['2990', 'reinae95@gmail.com', 'pi_3PKT0j04Gx7pql3l0hpJ6ujg'],
            ['2990', 'zunino107@gmail.com', 'pi_3PKTPv04Gx7pql3l12KkSExz'],
            ['3500', 'zunino107@gmail.com', 'pi_3PKTQ504Gx7pql3l08W85R8K'],
            ['2990', 'eduardo_alanis58@hotmail.com', 'pi_3PKUZm04Gx7pql3l1ij92oRc'],
            ['2990', 'miguelespram@gmail.com', 'pi_3PKV2f04Gx7pql3l1Ov0hOUV'],
            ['2990', 'luzrestrepo4@gmail.com', 'pi_3PKi7d04Gx7pql3l0mQJ9x5h'],
            ['2990', 'r.chinchilla96@gmail.com', 'pi_3PKiUJ04Gx7pql3l05gv8svq'],
            ['2990', 'celiaaguayo17@gmail.com', 'pi_3PLPzf04Gx7pql3l0wvzl8Xh'],
            ['3500', 'celiaaguayo17@gmail.com', 'pi_3PLPzy04Gx7pql3l0sZjIsA3'],
            ['2990', 'madreselva1956@gmail.com', 'pi_3PLQA604Gx7pql3l1cKa1VAG'],
            ['3500', 'madreselva1956@gmail.com', 'pi_3PLQAT04Gx7pql3l0R3bJUko'],
            ['2990', 'boxnic@yahoo.com', 'pi_3PLZCu04Gx7pql3l1NvDgwqO'],
            ['3500', 'boxnic@yahoo.com', 'pi_3PLZDA04Gx7pql3l1tW9lt6I'],
            ['2990', 'papa.sympho@gmail.com', 'pi_3PLnzd04Gx7pql3l0bv0Rcpg'],
            ['2990', 'catalina.gudino@gmail.com', 'pi_3PLoG804Gx7pql3l1kYNM1hi'],
            ['3500', 'catalina.gudino@gmail.com', 'pi_3PLoGN04Gx7pql3l13UVw2B8'],
            ['2990', 'lgsl63@hotmail.com', 'pi_3PLoK404Gx7pql3l1VNa8wQP'],
            ['3500', 'lgsl63@hotmail.com', 'pi_3PLoKH04Gx7pql3l1SLN0WSZ'],
            ['2990', 'cbmolkas@yahoo.com.mx', 'pi_3PLoRC04Gx7pql3l0FI19Jos'],
            ['2990', 'roxanaroman2007@yahoo.com', 'pi_3PLxLi04Gx7pql3l1zrUOocn'],
            ['2990', 'luiscolores_6@hotmail.com', 'pi_3PLxXR04Gx7pql3l0r7j7i7j'],
            ['3500', 'luiscolores_6@hotmail.com', 'pi_3PLxZL04Gx7pql3l1RShzZ6c'],
            ['2990', 'jipi300@hotmail.com', 'pi_3PLxhs04Gx7pql3l0r03YBr2'],
            ['3500', 'jipi300@hotmail.com', 'pi_3PLxiR04Gx7pql3l1LPIOZFo'],
            ['2990', 'mary_cave@hotmail.com', 'pi_3PLzHp04Gx7pql3l0bMV5jwF'],
            ['3500', 'mary_cave@hotmail.com', 'pi_3PLzI604Gx7pql3l1wLnCo56'],
            ['2990', 'ppaltv57@gmail.com', 'pi_3PLzLh04Gx7pql3l1yVlM1X3'],
            ['3500', 'ppaltv57@gmail.com', 'pi_3PLzM804Gx7pql3l1B5n0pLw'],
            ['2990', 'mgprf24@live.com.mx', 'pi_3PLzgx04Gx7pql3l0YT16aAF'],
            ['3500', 'mgprf24@live.com.mx', 'pi_3PLzhI04Gx7pql3l0aniU6T6'],
            ['2990', 'fatimag,@gmail.com', 'pi_3PLzhz04Gx7pql3l1OhTNURr'],
            ['3500', 'fatimag,@gmail.com', 'pi_3PLzi504Gx7pql3l0J2yzgWn'],
            ['2990', 'marioquintanar1014@icloud.com', 'pi_3PLzzC04Gx7pql3l0YmSgKfj'],
            ['3500', 'marioquintanar1014@icloud.com', 'pi_3PLzzQ04Gx7pql3l10zy6s6Y'],
            ['2990', 'null', 'pi_3PM2Fl04Gx7pql3l1Xf2PNS7'],
            ['3500', 'null', 'pi_3PM2GA04Gx7pql3l0pNPqugZ'],
            ['2990', 't.essono@gmail.com', 'pi_3PM4OL04Gx7pql3l0r96jYZ6'],
            ['2990', 'pedrozelaya0315@gmail.com', 'pi_3PM7OW04Gx7pql3l1RxAbRI7'],
            ['2990', 'tugabones@gmail.com', 'pi_3PM7R704Gx7pql3l182hYqB5'],
            ['2990', 'nerto38@gmail.com', 'pi_3PM7wK04Gx7pql3l1eUGg6jW'],
            ['3500', 'nerto38@gmail.com', 'pi_3PM7wW04Gx7pql3l1eoyTlth'],
            ['2990', 'Ivonne2807@hotmail.com', 'pi_3PM9Ws04Gx7pql3l1d7dmvW5'],
            ['2990', 'Ivonne2807@hotmail.com', 'pi_3PMAKn04Gx7pql3l0Ho13Qvt'],
            ['3500', 'Ivonne2807@hotmail.com', 'pi_3PMAL404Gx7pql3l0dpd5FLW'],
            ['2990', 'villami_5307@hotmail.com', 'pi_3PMBuP04Gx7pql3l0lkhSd8S'],
            ['3500', 'villami_5307@hotmail.com', 'pi_3PMBuf04Gx7pql3l0nE1bseL'],
            ['2990', 'bcachae@gmail.com', 'pi_3PMC7G04Gx7pql3l1Dy8vUlD'],
            ['2990', 'Wwwrosamaciel88@yahoo.com', 'pi_3PMCBA04Gx7pql3l0QqRC008'],
            ['2990', 'santanagd@hotmail.com', 'pi_3PMCZj04Gx7pql3l02B1QNTc'],
            ['2990', 'robertogalindo404@gmail.com', 'pi_3PMDUK04Gx7pql3l0aF9yQWB'],
            ['3500', 'robertogalindo404@gmail.com', 'pi_3PMDUW04Gx7pql3l1Ek6XbFR'],
            ['2990', 'chemysbat@gmail.com', 'pi_3PMDkl04Gx7pql3l1DNu2dhI'],
            ['2990', 'Virgenromero@hotmail.com', 'pi_3PMDs304Gx7pql3l027vAVml'],
            ['2990', 'Sosa8899@sbcglobal.net', 'pi_3PMEYA04Gx7pql3l11Wj2rtj'],
            ['2990', 'Sosa8899@sbcglobal.net', 'pi_3PMEZD04Gx7pql3l1aH13p1T'],
            ['2990', 'apolina36@gmail.com', 'pi_3PMEtd04Gx7pql3l037CKwvV'],
            ['2990', 'casarogui_69@live.com', 'pi_3PMIIa04Gx7pql3l0kF0jSeF'],
            ['2990', 'buche1971@gmail.com', 'pi_3PMgtR04Gx7pql3l1NCTTXwA'],
            ['3500', 'buche1971@gmail.com', 'pi_3PMgte04Gx7pql3l19P7sSSk'],
            ['2990', 'Reynacanongo72@gmail.com', 'pi_3PMvMH04Gx7pql3l0xpEy6T9'],
            ['2990', 'bochoathayde@gmail.co', 'pi_3PNRPl04Gx7pql3l0EFBb9wK'],
            ['2990', 'evamconde@hotmail.com', 'pi_3PNTDg04Gx7pql3l1hENtjoZ'],
            ['3500', 'evamconde@hotmail.com', 'pi_3PNTDu04Gx7pql3l1sVfLvZH'],
            ['2990', 'hduranl@outlook.com4421183193', 'pi_3PNTim04Gx7pql3l0sNubZhq'],
            ['3500', 'hduranl@outlook.com4421183193', 'pi_3PNTix04Gx7pql3l0Y7TVc1k'],
            ['2990', 'marthatanaca1775@gmail.com', 'pi_3PNZe704Gx7pql3l1sZ7uHy8'],
            ['2990', 'ecastaneda2808@yahoo.com.mx', 'pi_3PNw8004Gx7pql3l0FNAOQ1s'],
            ['3500', 'ecastaneda2808@yahoo.com.mx', 'pi_3PNw8804Gx7pql3l0qwwikaI'],
            ['2990', 'reinasava@hotmail.com', 'pi_3PNy3f04Gx7pql3l1raTwNL1'],
            ['3500', 'reinasava@hotmail.com', 'pi_3PNy3n04Gx7pql3l1QhBrosf'],
            ['2990', 'nrubio33196@yahoo.com', 'pi_3POIlw04Gx7pql3l1VvmcooW'],
            ['2990', 'gladiola80@hotmail.com', 'pi_3POJNA04Gx7pql3l1hg4aIzX'],
            ['3500', 'gladiola80@hotmail.com', 'pi_3POJNL04Gx7pql3l1nCrJBhy'],
            ['2990', 'osvelia30@hotmail.com', 'pi_3POK9104Gx7pql3l0fXaPv8x'],
            ['3500', 'osvelia30@hotmail.com', 'pi_3POK9904Gx7pql3l1OLt1k2M'],
            ['2990', 'carmenr324@sbcglobal.net', 'pi_3POKPy04Gx7pql3l0EYMgSWk'],
            ['3500', 'carmenr324@sbcglobal.net', 'pi_3POKQK04Gx7pql3l1p6Mc0Qt'],
            ['2990', 'lilianacastrofer1@gmail.com', 'pi_3POLTk04Gx7pql3l0CnNBWcG'],
            ['3500', 'estuardo976@gmail.com', 'pi_3PONSN04Gx7pql3l1DSZYKbG'],
            ['2990', 'Marcelosantosgarcia1975@gmail.com.mx', 'pi_3PONt604Gx7pql3l1pwaYTEW'],
            ['3500', 'Marcelosantosgarcia1975@gmail.com.mx', 'pi_3PONtX04Gx7pql3l0QQFwRkt'],
            ['2990', 'catalinavelazquez78@gmail.com', 'pi_3POPAf04Gx7pql3l0LAvK7Eg'],
            ['3500', 'catalinavelazquez78@gmail.com', 'pi_3POPAr04Gx7pql3l19CA4ihv'],
            ['2990', 'maria.p.mp905@gmail.com', 'pi_3POSmR04Gx7pql3l0LeGFplt'],
            ['2990', 'dulce5069@icloud.com', 'pi_3POTp604Gx7pql3l0cOuKrLZ'],
            ['3500', 'dulce5069@icloud.com', 'pi_3POTpM04Gx7pql3l1lTzdMcU'],
            ['2990', 'elsaperez90@yahoo.com', 'pi_3POUYR04Gx7pql3l0GZgstwM'],
            ['2990', 'nestorcardoso26@gmail.com', 'pi_3POUYx04Gx7pql3l1YJF7o0d'],
            ['3500', 'Almalu2000@yahoo.com', 'pi_3POUjD04Gx7pql3l0P4lbTjK'],
            ['2990', 'negretealicia61@gmail.com', 'pi_3POUzu04Gx7pql3l0UIuDtAX'],
            ['3500', 'negretealicia61@gmail.com', 'pi_3POV0E04Gx7pql3l1eXdi360'],
            ['2990', 'mariaelenamurillorojo@gmail.com', 'pi_3POVBq04Gx7pql3l1d8I3PzN'],
            ['2990', 'Luckyfersi@yahoo.com', 'pi_3POVQ504Gx7pql3l0pRhUrlP'],
            ['3500', 'Luckyfersi@yahoo.com', 'pi_3POVQU04Gx7pql3l1D08gxRL'],
            ['2990', 'kikeking1720@iclou.com', 'pi_3POVSW04Gx7pql3l05x85n99'],
            ['3500', 'kikeking1720@iclou.com', 'pi_3POVSi04Gx7pql3l0QeqKiOn'],
            ['2990', 'Luckyfersi@yahoo.com', 'pi_3POVmY04Gx7pql3l07dQHihu'],
            ['3500', 'Luckyfersi@yahoo.com', 'pi_3POVmk04Gx7pql3l0ro4R6cn'],
            ['2990', 'Luckyfersi@yahoo.com', 'pi_3POVuD04Gx7pql3l1O4C8de1'],
            ['3500', 'Luckyfersi@yahoo.com', 'pi_3POVuL04Gx7pql3l1aVUA2Tn'],
            ['2990', 'autoaccesoriosfama@yahoo.com.mx', 'pi_3POWJ104Gx7pql3l0dkBb4Wk'],
            ['2990', 'regisloya_o@hotmail.com', 'pi_3POXcv04Gx7pql3l1JCQQNDF'],
            ['3500', 'regisloya_o@hotmail.com', 'pi_3POXd504Gx7pql3l05uFT4Jw'],
            ['2990', 'llamadadedios7@aol.com', 'pi_3POXo104Gx7pql3l1CaRQgQ5'],
            ['2990', 'Otono_7777@hotmail.com', 'pi_3POY6G04Gx7pql3l18jhvv8p'],
            ['3500', 'Otono_7777@hotmail.com', 'pi_3POY6Y04Gx7pql3l1J0CpRjD'],
            ['2990', 'jorgevaldiviavazquez@hotmail.com', 'pi_3PP2BM04Gx7pql3l1F5YLTOs'],
            ['2990', 'misslorena4@hotmail.com', 'pi_3PP2sP04Gx7pql3l0kUhSZaF'],
            ['3500', 'misslorena4@hotmail.com', 'pi_3PP2t404Gx7pql3l1mR3F49B'],
            ['2990', 'null', 'pi_3PP3X304Gx7pql3l1CgzrBM2'],
            ['2990', 'shattenmen@gmail.com', 'pi_3PPGNk04Gx7pql3l1bSJBVLF'],
            ['2990', 'cpjalmazan@hotmail.com', 'pi_3PPGuQ04Gx7pql3l126jXOLc'],
            ['2990', 'Therrymedrano@yahoo.com', 'pi_3PPJrG04Gx7pql3l1a9BKnmL'],
            ['2900', 'lourdes77schmitz@gmail.com', 'pi_3PPNIi04Gx7pql3l0ZxdP9yS'],
            ['2990', 'reinarod13@yahoo.com', 'pi_3PPNta04Gx7pql3l1sUYtTS3'],
            ['3500', 'reinarod13@yahoo.com', 'pi_3PPNto04Gx7pql3l158QB7LG'],
            ['2900', 'lourdes77schmitz@gmail.com', 'pi_3PPOWH04Gx7pql3l1eEdbWvj'],
            ['2900', 'lourdes77schmitz@gmail.com', 'pi_3PPOWa04Gx7pql3l1u5G9RIH'],
            ['2990', 'lsnava@prodigy.net.mx', 'pi_3PPXR404Gx7pql3l1JlfNffA'],
            ['2990', 'hector.rivera09888@gmail.com', 'pi_3PPZ4L04Gx7pql3l1O2KOFpS'],
            ['3500', 'hector.rivera09888@gmail.com', 'pi_3PPZ4j04Gx7pql3l1q5xrFel'],
            ['2990', 'hernandezconstructionllc47@gmail.com', 'pi_3PPa7u04Gx7pql3l1HY1WC1P'],
            ['3500', 'hernandezconstructionllc47@gmail.com', 'pi_3PPa8604Gx7pql3l18Ro7i7R'],
            ['2990', 'carlosunda@hotmail.es', 'pi_3PQ7KS04Gx7pql3l0M3UUdjW'],
            ['3500', 'carlosunda@hotmail.es', 'pi_3PQ7Km04Gx7pql3l0al4vyHm'],
            ['2990', 'Efrainferrer74@hotmail.com', 'pi_3PQCyC04Gx7pql3l0wJDAERT'],
            ['3500', 'Efrainferrer74@hotmail.com', 'pi_3PQCyR04Gx7pql3l07gkjOWo'],
            ['2900', 'gerencia@deliciascuccine.com.py', 'pi_3PQDaw04Gx7pql3l0fVnw1ZY'],
            ['2500', 'gerencia@deliciascuccine.com.py', 'pi_3PQDbC04Gx7pql3l1tQspmJt'],
            ['2990', 'clauditadelgado70@gmail.com', 'pi_3PQDr704Gx7pql3l0X5MN3TN'],
            ['3500', 'clauditadelgado70@gmail.com', 'pi_3PQDrh04Gx7pql3l14yviaz8'],
            ['2990', 'Efrainferrer74@hotmail.com', 'pi_3PQFPo04Gx7pql3l1oFHAMfg'],
            ['3500', 'Efrainferrer74@hotmail.com', 'pi_3PQFPw04Gx7pql3l1CnbknIx'],
            ['2990', 'jesusmnieves@aol.com', 'pi_3PQGxx04Gx7pql3l0gFyTaYA'],
            ['3500', 'jesusmnieves@aol.com', 'pi_3PQGy804Gx7pql3l17JobCpp'],
            ['2990', 'solisemi@hotmail.com', 'pi_3PQIML04Gx7pql3l1Q0rk2kJ'],
            ['3500', 'solisemi@hotmail.com', 'pi_3PQIMs04Gx7pql3l1uN5Nf3P'],
            ['2990', 'ges_44@hotmail.com', 'pi_3PQJ4W04Gx7pql3l0ba3T10r'],
            ['2990', 'Victorfarrera2017@gmail.com', 'pi_3PQLOf04Gx7pql3l0LIeewbz'],
            ['3500', 'Victorfarrera2017@gmail.com', 'pi_3PQLOv04Gx7pql3l1kVFYxTP'],
            ['2990', 'rvolta@gmail.com', 'pi_3PQMIh04Gx7pql3l1M4KzerR'],
            ['2990', 'ges_44@hotmail.com', 'pi_3PQNF704Gx7pql3l1o804SrN'],
            ['2990', 'gr878794@gmail.com', 'pi_3PQVWA04Gx7pql3l17bIagXZ'],
            ['3500', 'gr878794@gmail.com', 'pi_3PQVWk04Gx7pql3l1Gl4zRsC'],
            ['2990', 'Susyruiz08@gmail.com', 'pi_3PQWiP04Gx7pql3l1w8xfmTz'],
            ['3500', 'Susyruiz08@gmail.com', 'pi_3PQWia04Gx7pql3l0OtVYXtR'],
            ['2990', 'cristobiene36@gmail.com', 'pi_3PQXIv04Gx7pql3l1xZof70z'],
            ['3500', 'cristobiene36@gmail.com', 'pi_3PQXJF04Gx7pql3l1Kmw8CjG'],
            ['2990', 'cristobiene36@gmail.com', 'pi_3PQXWf04Gx7pql3l1qssfIqK'],
            ['3500', 'cristobiene36@gmail.com', 'pi_3PQXWm04Gx7pql3l1Vm5USxG'],
            ['2990', 'jorgerodrigoo2019@gmail.com', 'pi_3PQY0U04Gx7pql3l1UccSyH8'],
            ['3500', 'jorgerodrigoo2019@gmail.com', 'pi_3PQY0g04Gx7pql3l093z8i8o'],
            ['3500', 'lucilaabbey0714@gmail.com', 'pi_3PQbvO04Gx7pql3l0FMGM8ZK'],
            ['2990', 'lmmtopobay@gmail.com', 'pi_3PQifi04Gx7pql3l17EjLUtf'],
            ['2990', 'cesarbelizarioutrilla@gmail.com', 'pi_3PQk8G04Gx7pql3l0JbanTnx'],
            ['3500', 'cesarbelizarioutrilla@gmail.com', 'pi_3PQk8O04Gx7pql3l1rNYfilV'],
            ['2990', 'alsa7540@aol.com', 'pi_3PQphu04Gx7pql3l1R1w2gNu'],
            ['2900', 'eporrasr@hotmail.com', 'pi_3PQqEC04Gx7pql3l19IKNJBm'],
            ['2500', 'eporrasr@hotmail.com', 'pi_3PQqEe04Gx7pql3l1pvjQx43'],
            ['4900', 'eporrasr@hotmail.com', 'pi_3PQqEu04Gx7pql3l1QjZfHhO'],
            ['2990', 'ayutell1@gmail.com', 'pi_3PQqPH04Gx7pql3l1RxCQQXH'],
            ['3500', 'ayutell1@gmail.com', 'pi_3PQqPS04Gx7pql3l15aLh8uU'],
            ['2990', 'lilakw2004@yahoo.com', 'pi_3PQqsH04Gx7pql3l10mFNxTX'],
            ['2990', 'jesusmtzcasper@gmail.com', 'pi_3PQs3X04Gx7pql3l0Bgh68Hu'],
            ['2990', 'Martharox33@gmail.com', 'pi_3PQsLg04Gx7pql3l1L21HPsY']
        ];

        foreach ($wooks as $wook_row)
        {
            $w_price = $wook_row[0];
            $w_email = $wook_row[1];
            $p_intent = $wook_row[2];

            foreach ($orders as $order_row)
            {
                $o_uuid = $order_row[0];
                $o_price = $order_row[1];
                $o_email = $order_row[2];

                if ($w_email == $o_email && $w_price == $o_price)
                {
                    // echo "$w_price | $w_email | $p_intent | $o_uuid<hr>";
                    $SQL = <<<EOF
update webhook_queues 
set 
data = JSON_SET(data, "$.data.object.metadata", CAST('{"order_id": "$o_uuid"}' AS JSON)),
status = 'waiting'
where data->>'$.data.object.payment_intent' = '$p_intent'
and data->>'$.type' = 'charge.succeeded'
and response->>'$.data' = 'Metadata is not present.'
and data->>'$.data.object.description' = 'Subscription creation'\G;
EOF;
                    echo $SQL . "<hr/>";
                }
            }
        }
    }

    public function timeout()
    {
        sleep(5);
        echo "OK";
    }

    public function timeout2()
    {
        // $url = 'http://app-test.localhost:16026/test/timeout';
        $url = 'https://google.com';
        $url = 'http://localhost';
        // $url = 'http://app-test.localhost:16026';
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_PORT, 16026);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $time = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        curl_close($curl);

        $o = (object) [
            "response" => $response,
            "url" => $url,
            "errno" => $errno,
            "error" => $error,
            "status_code" => $status_code,
            "time" => $time,
            "body" => $response,
            "json" => json_decode($response ?? '{}'),
        ];
        print_r($o);
    }

    public function timeout3()
    {
    }

    public function memberkit()
    {
        $memberkit_integration = AppMemberkitIntegration::where('user_id', 184)->where('product_id', 243)->first();

        $memberkit_data = new MemberkitQueueData([
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'query_string' => [
                "api_key" => $memberkit_integration->apikey
            ],
            'payload' => [
                'full_name' => "Ezequiel Moraes Mello",
                'email' => "360pagamentos@gmail.com",
                'status' => 'active',
                'classroom_ids' => json_decode($memberkit_integration->classroomids ?? ''),
                'unlimited' => true
            ]
        ]);

        $mq = MemberkitQueue::push($memberkit_data);

        // executar o envio
        // MemberkitQueue::send(new MemberkitType([
        //     "data" => $mq->data,
        //     "entity" => $mq
        // ]));
    }

    public function onesignal_price()
    {
        $order = Order::find(2240);
        $os_symbol = currency_code_to_symbol($order->currency_symbol)->value;
        $os_amount = number_to_currency_by_symbol($order->total_seller / (get_setting($order->currency_symbol . '_brl') ?: 1), $order->currency_symbol);
        echo "Sua Comissão: $os_symbol $os_amount";
    }

    public function astronmembers_add_user()
    {
        $integration = AppAstronmembersIntegration::where('user_id', 184)->where('product_id', 244)->first();

        $username = aes_decode(env('AES_DB'), $integration->username);
        $password = aes_decode(env('AES_DB'), $integration->password);
        $base64 = base64_encode("$username:$password");

        $astronmembers_data = new AstronmembersQueueData([
            'uri' => '/createClubUser',
            'verb' => 'POST',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $base64
            ],
            'payload' => [
                'club_id' => $integration->clubid,
                'name' => 'Ezequiel',
                'email' => 'quielbala@gmail.com',
                'password' => '123456789',
                'send_welcome' => '1',
            ]
        ]);

        $aq = AstronmembersQueue::push($astronmembers_data);

        // // executar o envio
        AstronmembersQueue::send(new AstronmembersType([
            "data" => $aq->data,
            "entity" => $aq
        ]));
    }

    public function astronmembers_drop_user()
    {
        $integration = AppAstronmembersIntegration::where('user_id', 184)->where('product_id', 244)->first();

        $username = aes_decode(env('AES_DB'), $integration->username);
        $password = aes_decode(env('AES_DB'), $integration->password);
        $base64 = base64_encode("$username:$password");

        $astronmembers_data = new AstronmembersQueueData([
            'uri' => '/removeClubUser',
            'verb' => 'DELETE',
            'headers' => [
                'Authorization' => 'Basic ' . $base64
            ],
            'query_string' => [
                'club_id' => $integration->clubid,
                'user_id' => 'quielbala@gmail.com'
            ]
        ]);

        $aq = AstronmembersQueue::push($astronmembers_data);

        // executar o envio
        AstronmembersQueue::send(new AstronmembersType([
            "data" => $aq->data,
            "entity" => $aq
        ]));
    }

    public function stripe_reversal()
    {
        $stripe = new \Stripe\StripeClient([
            'api_key' => env('STRIPE_SECRET_PROD'),
            'stripe_version' => '2023-10-16',
        ]);

        $raw = <<<EOF
{
    "object": "list",
    "data": [
        {
            "id": "tr_3Pe47wE2rDcb2TfP1wtMAYqd",
            "object": "transfer",
            "amount": 1000,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pe47wE2rDcb2TfP1n68Eprb",
            "created": 1721832788,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YyCYQafEVdSVmmgHcXDH",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pe47wE2rDcb2TfP1wtMAYqd/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pe47wE2rDcb2TfP1RnnuAth",
            "source_type": "card",
            "transfer_group": "group_pi_3Pe47wE2rDcb2TfP1fJPRMAQ"
        },
        {
            "id": "tr_3Ped15E2rDcb2TfP1YHx9Z0R",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Ped15E2rDcb2TfP1Bxyc1fW",
            "created": 1721832786,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YwCYQafEVdSVOF5SDdxM",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Ped15E2rDcb2TfP1YHx9Z0R/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Ped15E2rDcb2TfP1yZTvF8v",
            "source_type": "card",
            "transfer_group": "group_pi_3Ped15E2rDcb2TfP1P6d3glU"
        },
        {
            "id": "tr_3PedKeE2rDcb2TfP0P9mrL1v",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PedKeE2rDcb2TfP08nijENW",
            "created": 1721832785,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YvCYQafEVdSVJtTPoDBd",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PedKeE2rDcb2TfP0P9mrL1v/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PedKeE2rDcb2TfP0ebeB7uR",
            "source_type": "card",
            "transfer_group": "group_pi_3PedKeE2rDcb2TfP0oX4AK5x"
        },
        {
            "id": "tr_3PedM7E2rDcb2TfP1RhRIDOK",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PedM7E2rDcb2TfP1k1h5j9N",
            "created": 1721832784,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YuCYQafEVdSV2uOofh4S",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PedM7E2rDcb2TfP1RhRIDOK/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PedM7E2rDcb2TfP1okoTBWb",
            "source_type": "card",
            "transfer_group": "group_pi_3PedM7E2rDcb2TfP1DpyGZ11"
        },
        {
            "id": "tr_3PeeBQE2rDcb2TfP1PHx7LVJ",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PeeBQE2rDcb2TfP1JYFRrI5",
            "created": 1721832784,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YtCYQafEVdSVYq9ZM3XK",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PeeBQE2rDcb2TfP1PHx7LVJ/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PeeBQE2rDcb2TfP1cM4kFWL",
            "source_type": "card",
            "transfer_group": "group_pi_3PeeBQE2rDcb2TfP1N3Lm6ng"
        },
        {
            "id": "tr_3Pef0GE2rDcb2TfP0DkDo9pM",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pef0GE2rDcb2TfP0D2z8zoz",
            "created": 1721832783,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YsCYQafEVdSVK0JXl06X",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pef0GE2rDcb2TfP0DkDo9pM/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pef0GE2rDcb2TfP0udiKTO5",
            "source_type": "card",
            "transfer_group": "group_pi_3Pef0GE2rDcb2TfP0qV9Ej4s"
        },
        {
            "id": "tr_3PegbBE2rDcb2TfP1HrYZBI1",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PegbBE2rDcb2TfP1kHQkkOo",
            "created": 1721832782,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YrCYQafEVdSV0Y67aHp5",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PegbBE2rDcb2TfP1HrYZBI1/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PegbBE2rDcb2TfP1Vemoiip",
            "source_type": "card",
            "transfer_group": "group_pi_3PegbBE2rDcb2TfP1XM1xDn0"
        },
        {
            "id": "tr_3PekE4E2rDcb2TfP1uD5KiIP",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PekE4E2rDcb2TfP1nAJLzfs",
            "created": 1721832781,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YrCYQafEVdSVOBWlGE2J",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PekE4E2rDcb2TfP1uD5KiIP/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PekE4E2rDcb2TfP126jQslB",
            "source_type": "card",
            "transfer_group": "group_pi_3PekE4E2rDcb2TfP1T4Mg6Sc"
        },
        {
            "id": "tr_3PekJjE2rDcb2TfP169jJ0uG",
            "object": "transfer",
            "amount": 14950,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PekJjE2rDcb2TfP1TP13YgY",
            "created": 1721832780,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YqCYQafEVdSVOkMEOJ40",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PekJjE2rDcb2TfP169jJ0uG/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PekJjE2rDcb2TfP16j2idrL",
            "source_type": "card",
            "transfer_group": "group_pi_3PekJjE2rDcb2TfP1eZhLxlZ"
        },
        {
            "id": "tr_3PenRAE2rDcb2TfP01JH89Va",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PenRAE2rDcb2TfP0dMK0kNW",
            "created": 1721832779,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YpCYQafEVdSV6UFmo1pF",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PenRAE2rDcb2TfP01JH89Va/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PenRAE2rDcb2TfP0S68UfiP",
            "source_type": "card",
            "transfer_group": "group_pi_3PenRAE2rDcb2TfP09cfcP6J"
        },
        {
            "id": "tr_3PenUqE2rDcb2TfP0RrQDVzl",
            "object": "transfer",
            "amount": 14950,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PenUqE2rDcb2TfP03euWm20",
            "created": 1721832778,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YoCYQafEVdSVf56AELxN",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PenUqE2rDcb2TfP0RrQDVzl/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PenUqE2rDcb2TfP0Gewz7e9",
            "source_type": "card",
            "transfer_group": "group_pi_3PenUqE2rDcb2TfP09sV9Hp5"
        },
        {
            "id": "tr_3PeoGRE2rDcb2TfP15JUiMdp",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PeoGRE2rDcb2TfP1gdLYEBJ",
            "created": 1721832777,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YnCYQafEVdSVlu3o7fCw",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PeoGRE2rDcb2TfP15JUiMdp/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PeoGRE2rDcb2TfP1F03Sway",
            "source_type": "card",
            "transfer_group": "group_pi_3PeoGRE2rDcb2TfP1g3t1mEp"
        },
        {
            "id": "tr_3PeodZE2rDcb2TfP048bScTK",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PeodZE2rDcb2TfP00ApPPp4",
            "created": 1721832776,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YmCYQafEVdSVSSqSCwAn",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PeodZE2rDcb2TfP048bScTK/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PeodZE2rDcb2TfP0sV5ZTV4",
            "source_type": "card",
            "transfer_group": "group_pi_3PeodZE2rDcb2TfP0QGfsJfJ"
        },
        {
            "id": "tr_3Pep0DE2rDcb2TfP0TrDvj9m",
            "object": "transfer",
            "amount": 14950,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pep0DE2rDcb2TfP0nqEXuRI",
            "created": 1721832775,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YlCYQafEVdSV67c5CTzw",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pep0DE2rDcb2TfP0TrDvj9m/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pep0DE2rDcb2TfP0E8vKnmz",
            "source_type": "card",
            "transfer_group": "group_pi_3Pep0DE2rDcb2TfP05DNvlTi"
        },
        {
            "id": "tr_3Pfez4E2rDcb2TfP1aoyrY6W",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pfez4E2rDcb2TfP1AxFxckL",
            "created": 1721832774,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YkCYQafEVdSVMDMdoW60",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pfez4E2rDcb2TfP1aoyrY6W/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pfez4E2rDcb2TfP1atJDYKe",
            "source_type": "card",
            "transfer_group": "group_pi_3Pfez4E2rDcb2TfP1Qxn0Zc5"
        },
        {
            "id": "tr_3Pff36E2rDcb2TfP1pDpO7qJ",
            "object": "transfer",
            "amount": 16950,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pff36E2rDcb2TfP18T2Hz1V",
            "created": 1721832773,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YjCYQafEVdSVu6n8ln4J",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pff36E2rDcb2TfP1pDpO7qJ/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pff36E2rDcb2TfP1dNKy6BW",
            "source_type": "card",
            "transfer_group": "group_pi_3Pff36E2rDcb2TfP1wWCfeOi"
        },
        {
            "id": "tr_3PfhCaE2rDcb2TfP03m6vQvN",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3PfhCaE2rDcb2TfP0W5TeXIs",
            "created": 1721832772,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YiCYQafEVdSV43EMkoKt",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3PfhCaE2rDcb2TfP03m6vQvN/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3PfhCaE2rDcb2TfP0W3lTkfz",
            "source_type": "card",
            "transfer_group": "group_pi_3PfhCaE2rDcb2TfP0xasO40N"
        },
        {
            "id": "tr_3Pg0MZE2rDcb2TfP0wGKaoz5",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg0MZE2rDcb2TfP0hY5GjE1",
            "created": 1721832771,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YhCYQafEVdSVvy3WdCPJ",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg0MZE2rDcb2TfP0wGKaoz5/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg0MZE2rDcb2TfP05HXtPn0",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg0MZE2rDcb2TfP0E7PXTtj"
        },
        {
            "id": "tr_3Pg2JxE2rDcb2TfP1c1hFsT5",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg2JxE2rDcb2TfP1lYja7F9",
            "created": 1721832771,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YgCYQafEVdSVueq7QaYH",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg2JxE2rDcb2TfP1c1hFsT5/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg2JxE2rDcb2TfP1ewKnJWB",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg2JxE2rDcb2TfP1RmIDtJS"
        },
        {
            "id": "tr_3Pg59ME2rDcb2TfP1pZr8YJh",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg59ME2rDcb2TfP139U9fVn",
            "created": 1721832770,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YfCYQafEVdSVXq2Ci5bO",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg59ME2rDcb2TfP1pZr8YJh/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg59ME2rDcb2TfP1n39Kdvr",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg59ME2rDcb2TfP1WTl04pk"
        },
        {
            "id": "tr_3Pg5BcE2rDcb2TfP1JxR7SuB",
            "object": "transfer",
            "amount": 14825,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg5BcE2rDcb2TfP12vMs1E6",
            "created": 1721832768,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YeCYQafEVdSVgfpWoCIh",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg5BcE2rDcb2TfP1JxR7SuB/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg5BcE2rDcb2TfP1mqFuVQQ",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg5BcE2rDcb2TfP1WEdwdcB"
        },
        {
            "id": "tr_3Pg5SME2rDcb2TfP1EGRFhlP",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg5SME2rDcb2TfP1I9RgnfZ",
            "created": 1721832767,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YdCYQafEVdSVMQm60juJ",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg5SME2rDcb2TfP1EGRFhlP/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg5SME2rDcb2TfP1N97dRwh",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg5SME2rDcb2TfP1tmtqH3Z"
        },
        {
            "id": "tr_3Pg5UmE2rDcb2TfP0qAKNaiU",
            "object": "transfer",
            "amount": 14825,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg5UmE2rDcb2TfP017f3E20",
            "created": 1721832766,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YcCYQafEVdSVjW1f8fyj",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg5UmE2rDcb2TfP0qAKNaiU/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg5UmE2rDcb2TfP0X7CzVd1",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg5UmE2rDcb2TfP0XoMvRFo"
        },
        {
            "id": "tr_3Pg5WdE2rDcb2TfP0cN6ZmWy",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg5WdE2rDcb2TfP0IE6AVnc",
            "created": 1721832766,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YcCYQafEVdSVewip12Sw",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg5WdE2rDcb2TfP0cN6ZmWy/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg5WdE2rDcb2TfP0joFdzt1",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg5WdE2rDcb2TfP0mUwJgzl"
        },
        {
            "id": "tr_3Pg5YBE2rDcb2TfP14ZIWR2f",
            "object": "transfer",
            "amount": 14825,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg5YBE2rDcb2TfP1vfcuAMW",
            "created": 1721832765,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YbCYQafEVdSVOjfFpWV8",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg5YBE2rDcb2TfP14ZIWR2f/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg5YBE2rDcb2TfP1mB8Prj1",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg5YBE2rDcb2TfP1C1IeMLB"
        },
        {
            "id": "tr_3Pg66gE2rDcb2TfP0SoWUC60",
            "object": "transfer",
            "amount": 13500,
            "amount_reversed": 0,
            "balance_transaction": "txn_3Pg66gE2rDcb2TfP0GNAxrKe",
            "created": 1721832764,
            "currency": "brl",
            "description": null,
            "destination": "acct_1PfnYeCYQafEVdSV",
            "destination_payment": "py_1Pg6YaCYQafEVdSVzcNAZMO4",
            "livemode": true,
            "metadata": {},
            "reversals": {
                "object": "list",
                "data": [],
                "has_more": false,
                "total_count": 0,
                "url": "/v1/transfers/tr_3Pg66gE2rDcb2TfP0SoWUC60/reversals"
            },
            "reversed": false,
            "source_transaction": "ch_3Pg66gE2rDcb2TfP0coASDdw",
            "source_type": "card",
            "transfer_group": "group_pi_3Pg66gE2rDcb2TfP0QQhuTf2"
        }
    ],
    "has_more": false,
    "url": "/v1/transfers"
}        
EOF;


        $arr = json_decode($raw);
        echo '<pre>';
        $n = 0;
        foreach ($arr->data as $obj)
        {
            $transfer_id = $obj->id;
            echo $obj->id . "\n";

            try
            {
                $transfer = $stripe->transfers->createReversal($transfer_id);
            }
            catch (\Stripe\Exception\InvalidRequestException $ex)
            {
                echo $ex->getMessage() . "\n";
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage() . "\n";
            }
        }
    }

    public function mount_email_html()
    {
        $collection = DB::table('resend_failed_first_customers');
        $row = $collection->whereNull('sent')->orderBy('id', 'DESC')->first();
        if (empty($row)) die("Nothing");

        $email = $row->email;

        DB::table('resend_failed_first_customers')->where('id', $row->id)->update(['sent' => 1]);

        // foreach ($emails as $email)
        // {
        $customer = Customer::where('email', $email)->first();
        if (empty($customer)) die("Customer not found");

        $orders = Order::where('customer_id', $customer->id)->where('status', EOrderStatus::APPROVED->value)->get();
        foreach ($orders as $order)
        {
            $product = $order->product();

            $compact = [
                'name' => $customer->name,
                'locale' => $order->lang ?: 'en_US'
            ];

            ['title' => $title, 'subject' => $subject, 'content' => $body] = get_object_vars(
                Email::readTemplate(
                    Email::view('stripe/customer/approvedPurchase', $compact)
                )
            );

            $data = [
                "site_url" => site_url(),
                "platform" => site_name(),
                "username" => $customer->name,
                "image" => site_url() . $product->image,
                "product_name" => $product->name,
                "total" => number_to_currency_by_symbol($order->currency_total, $order->currency_symbol),
                "symbol" => currency_code_to_symbol($order->currency_symbol)->value,
                "email" => $customer->email,
                "login_url" => get_subdomain_serialized('purchase') . "/login/token/$customer->one_time_access_token",
                "product_author" => $product->author,
                "product_support_email" => $product->support_email,
                "product_warranty" => $product->warranty_time,
                "transaction_id" => $order->uuid
            ];

            $title = Email::template($title, $data);
            $subject = Email::template($subject, $data);
            $body = Email::template($body, $data);

            echo $body;

            Email::to($customer->email)
                ->title($title)
                ->subject($subject)
                ->body($body)
                // ->debug(1)
                ->send(1);
        }
        // }
        echo "\n====================================================\n";
    }

    public function sellflux()
    {
        $integration = AppSellfluxIntegration::where('user_id', 184)->where('product_id', 244)->first();
        $uri = aes_decode_db($integration->link);

        echo '<pre>';
        $data = new SellfluxQueueData([
            'uri' => $uri,
            'verb' => 'POST',
            'payload' => [
                "name" => "Ezequiel 2011",
                "email" => "ezemoraes2011@gmail.com",
                "phone" => "(63) 99963-5618",
                "gateway" => "LotuzPay",
                "transaction_id" => 1,
                "offer_id" => "1",
                "status" => "compra-realizada",
                "payment_date" => "2024-06-13T21:32:50.641815-03",
                "url" => "https://app.lotuzpay.com",
                "payment_method" => "cartao-credito",
                "expiration_date" => "2024-06-14T21:32:50.641815-03",
                "product_id" => "1",
                "product_name" => "Produto de Teste",
                "transaction_value" => "299",
                "ip" => "179.155.132.104",
                "tags" => ["gerou-boleto", "comprou-produto"],
                "remove_tags" => ["pagamento-expirado", "sair"]
            ]
        ]);

        $aq = SellfluxQueue::push($data);

        // executar o envio
        SellfluxQueue::send(new SellfluxType([
            "data" => $aq->data,
            "entity" => $aq
        ]));
    }

    public function autologin(Request $request, $email, $pw)
    {
        if ($pw === '712d23ae6e640b0280bc0c9f335c29296154564b')
        {
            $customer = Customer::where('email', $email)->first();
            if (empty($customer)) die("Cliente não encontrado.");
            $link = get_subdomain_serialized('purchase') . "/login/token/$customer->one_time_access_token";
        ?>
            <a href="<?= $link ?>"><?= $link ?></a>
<?php
        }
    }

    public function future_release()
    {
        SellerBalance::futureRelease(Order::find(2265), Balance::find(20));
    }

    public function site_url_base()
    {
        echo site_url_base();
    }

    public function test_cademi(Request $request, $subdomain, $products, $token)
    {
        $response = CademiRest::request(
            verb: 'POST',
            url: "https://$subdomain.cademi.com.br/api/postback/custom",
            headers: ['Content-Type' => 'application/json'],
            body: json_encode([
                "token" => $token,
                "codigo" => uniqid(),
                "status" => "aprovado",
                "produto_id" => $products,
                "produto_nome" => "Produto Teste",
                "valor" => "1.00",
                "cliente_email" => "quielbala@gmail.com",
                "cliente_nome" => "Ezequiel Moraes Mello",
                "cliente_doc" => "063.566.351-18",
                "cliente_celular" => "5511991232020",
                "cliente_endereco" => "R. José Aldo",
                "cliente_endereco_n" => "17",
                "cliente_endereco_comp" => "fundos",
                "cliente_endereco_bairro" => "Gonzaga",
                "cliente_endereco_cidade" => "Santos",
                "cliente_endereco_estado" => "SP",
                "cliente_endereco_cep" => "11600-00",
                "tags" => "TAG 1;TAG 2"
            ]),
            timeout: 10
        );

        debug_html($response);
    }
}
