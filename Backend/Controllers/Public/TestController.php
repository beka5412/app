<?php

namespace Backend\Controllers\Public;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Attributes\Route;
use Backend\Services\BancoInter\Instance as BancoInter;

class TestController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    #[Route(verb: 'GET', uri: '/test/now')]
    public function now(Request $request)
    {
        echo today();
    }

    #[Route(verb: 'GET', uri: '/test/thanks/pix')]
    public function thanks_pix(Request $request)
    {
        echo "<h1>Obrigado Pix</h1>";
        die();
    }

    #[Route(verb: 'GET', uri: '/test/thanks/credit-card')]
    public function thanks_credit_card(Request $request)
    {
        echo "<h1>Obrigado Cartão de Crédito</h1>";
    }

    #[Route(verb: 'GET', uri: '/test/bancointer')]
    public function transfer_pix()
    {
        echo "Iniciando...\n";

        print_r(
            BancoInter::pix('CHAVE_PIX', 2, "TESTE QUEBRA\nDE LINHA")
        );

        echo "\nFim";
    }

    #[Route(verb: 'GET', uri: '/test/bancointer/pix-webhook')]
    public function bancointer_webhook()
    {
        $instance = BancoInter::instance();
        $instance->access_token = $instance->token()->json?->access_token;

        print_r($instance->pixWebhook("https://rocketpays.app/wook/bancointer"));
    }
}