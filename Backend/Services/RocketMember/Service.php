<?php 

namespace Backend\Services\RocketMember;

// use Backend\App;
use Backend\Http\Request;

class Service
{
    // public App $application;
    public $url = '';

    // public function __construct(App $application)
    // {
    //     $this->application = $application;
    //     $this->url = env('ROCKETMEMBER_WEBHOOK');
    // }

    public function payload(Array $data=[])
    {
        $this->payload = $data;
        return $this;
    }

    public function send()
    {
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer 95ca9a6b8745856328a67e3452d820e7"
        ];
        $payload = json_encode($this->payload);
        $url = $this->url;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function test()
    {
        $response = RocketMember::payload([
            "product_id" => 1,
            "customer_id" => 2,
            "order_id" => 3,
        ])
        ->send();
        print_r($response);
    }

    public function api()
    {
        echo 'teste';
    }
}
// "{""product_id"":1,""customer_id"":2,""order_id"":3}"
// {
//     "product_id": 1,
//     "customer_id": 2,
//     "order_id": 3
// }