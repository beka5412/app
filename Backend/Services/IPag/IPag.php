<?php

namespace Backend\Services\IPag;

class IPag
{
    public $endpoint = 'https://api.ipag.com.br';
    
    public function creditCard($data)
    {
        $card = [
            "tokenize" => true,
            "holder" => $data["holdername"],
            "number" => $data["card_number"],
            "expiry_month" => $data["month"],
            "expiry_year" => $data["year"],
            "cvv" => $data["cvv"]
        ];

        if (!empty($token=($data["card_id"]??'')) && $data["use_saved_card"] == 1) $card = compact('token');

        $payload = [
            "amount" => $data["total"],
            "order_id" => $data["transaction_id"],
            "callback_url" => get_subdomain_serialized('checkout')."/wook/ipag",
            "payment" => [
                "type" => "card",
                "method" => $data["flag"],
                "installments" => $data["installments"]
            ],
            "customer" => $data['customer'],
            "payment" => [
                "type" => "card",
                "method" => $data["flag"],
                "installments" => 1,
                "card" => $card
            ],
        ];

        $response = $this->request(
            verb: 'GET', 
            url: "/service/payment",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2"],
            body: json_encode($payload)
        );

        return $response;
    }

    public function billet($data)
    {
        $response = $this->request(
            verb: 'GET', 
            url: "/service/payment",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2"],
            body: json_encode([
                "amount" => $data["total"],
                "order_id" => $data["transaction_id"],
                "callback_url" => get_subdomain_serialized('checkout')."/wook/ipag",
                "payment" => [
                    "type" => "boleto",
                    "method" => "boletopagseguro",
                    "installments" => 1,
                    "boleto" => [
                        "due_date" => date('d/m/Y', strtotime(date('Y-m-d') . ' + 3 days')),
                        "instructions" => ["Pagável em qualquer banco."]
                    ]
                ],
                "customer" => $data['customer']
            ])
        );
        return $response;
    } 

    public function pix($data)
    {
        $response = $this->request(
            verb: 'GET', 
            url: "/service/payment",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2"],
            body: json_encode([
                "amount" => $data["total"],
                "order_id" => $data["transaction_id"],
                "callback_url" => get_subdomain_serialized('checkout')."/wook/ipag",
                "payment" => [
                    "type" => "pix",
                    "method" => "pix",
                    "installments" => 1
                ],
                "customer" => $data['customer']
            ])
        );

        return $response;
    }

    public function subscription($data)
    {
        $card = [
            "tokenize" => true,
            "holder" => $data["holdername"],
            "number" => $data["card_number"],
            "expiry_month" => $data["month"],
            "expiry_year" => $data["year"],
            "cvv" => $data["cvv"]
        ];        

        if (!empty($token=($data["card_id"]??'')) && $data["use_saved_card"] == 1) $card = compact('token');

        $payload = [
            "amount" => $data["total"],
            "order_id" => $data["transaction_id"],
            "callback_url" => get_subdomain_serialized('checkout')."/wook/ipag",
            "payment" => [
                "type" => "card",
                "method" => $data["flag"],
                "installments" => $data["installments"]
            ],
            "customer" => $data['customer'],
            "payment" => [
                "type" => "card",
                "method" => $data["flag"],
                "installments" => 1,
                "card" => $card
            ]
        ];
        
        if (!empty($data["plan"])) $payload["subscription"] = [
            "frequency" => $data["plan"]["interval_count"] ?? 1,
            "interval" => $data["plan"]["interval"] ?? "month",
            "start_date" => date("Y-m-d")
        ];

        $response = $this->request(
            verb: 'GET', 
            url: "/service/payment",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2"],
            body: json_encode($payload)
        );

        return $response;
    }
    
    public function consult($transaction_id, $param="tid")
    {
        return $this->request(
            verb: 'GET',
            url: "/service/consult?$param=$transaction_id",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2", "accept" => "application/json"]
        );
    }

    public function consultToken($token)
    {
        return $this->request(
            verb: 'GET',
            url: "/service/resources/card_tokens?token=$token",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2", "accept" => "application/json"]
        );
    }
    
    public function consultSubscription($subscription_id)
    {
        return $this->request(
            verb: 'GET',
            url: "/service/resources/subscriptions?id=$subscription_id",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2", "accept" => "application/json"]
        );
    }

    public function cancelSubscription($subscription_id)
    {
        return $this->request(
            verb: 'PUT',
            url: "/service/resources/subscriptions?id=$subscription_id",
            headers: ["Content-Type" => "application/json", "x-api-version" => "2", "accept" => "application/json"],
            body: json_encode(['is_active' => false])
        );
    }

    public function getCards($cards)
    {
        return array_map(fn($card) => $this->consultToken($card), $cards);
    }

    private function request($verb, $url, $headers=[], $body="")
    {
        $headers = array_to_header($headers); 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->endpoint.$url);
        curl_setopt($curl, CURLOPT_USERPWD, env('IPAG_ID').":".env('IPAG_KEY'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($body)) curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'IpagSdkPhp (+https://github.com/jhernandes/ipag-sdk-php/)');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $verb);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}