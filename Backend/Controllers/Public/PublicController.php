<?php

namespace Backend\Controllers\Public;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Attributes\Route;
use Backend\Models\User;
use Backend\Http\Link;

class PublicController
{
    public App $application;

    public string $title = '';
    public string $context = 'public';

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function render_image_base64(Request $request)
    {
        $image = $request->query('data');
        $image = explode(";base64,", $image)[1] ?? '';
        header("Content-Type: image/png");
        echo base64_decode($image);
    }

    public function redirect(Request $request, $url)
    {
        $url = hex2bin($url);
        echo file_get_contents($url);
    }

    public function terms()
    {
        $title = 'Termos';
        $context = $this->context;
        return View::render('frontend/view/public/termsView.php', compact('title', 'context'));
    }

    /**
     * Autenticar customer na URL atual
     *
     * @param Request $request
     * @return void
     */
    // public function auth_customer(Request $request)
    // {
    //     // ECHO '123';

    //     // $_SESSION['XXXX'] = 1;
    //     // print_r($_SESSION);
    //     // $access_token = $request->json()->access_token ?? '';
    //     // c_authenticate($access_token);
    //     // Response::json(["status" => "success", "access_token" => $access_token, "session" => $_SESSION]);
    // }

    // #[Route(verb: 'GET', uri: '/test/auth/customer')]
    // public function test(Request $req)
    // {
    //     $customer = \Backend\Models\Customer::where('email', 'quielbala@gmail.com')->first();
    //     $response = auth_customer($customer->access_token);

    //     $c_access_token = $response->session->c_access_token ?? '';
    //     $customer = $response->session->customer ?? '';
    //     $_SESSION = $_SESSION + compact('c_access_token', 'customer');
    // }

    // #[Route(verb: 'GET', uri: '/test/check_auth_customer', subdomain: 'checkout')]
    // public function check_auth_customer(Request $request)
    // {
    //     echo 'session:';
    //     print_r($_SESSION);
    //     // $_SESSION['LOCALHOST_6012'] = 'YES';
    //     // print_r($_SESSION);
    //     // setcookie("PURCHASE_CUS", "12345", time() + (86400 * 30), "/"); 
    //     // print_r($_COOKIE['PURCHASE_CUS']);
    // }
}