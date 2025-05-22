<?php

namespace Backend\Services\GetNet;

class GetNet
{
    public $endpoint = '';
    public $client_id = '';
    public $client_secret = '';

    public function __construct()
    {
        $this->endpoint = env('GETNET_ENDPOINT');
        $this->client_id = env('GETNET_CLIENT_ID');
        $this->client_secret = env('GETNET_CLIENT_SECRET');
    }

    public function accessToken()
    {
        $object = (array) json_decode($this->auth());
        extract($object);
        return $access_token;
    }
    
    public function tokenizeCard($access_token, $data)
    {
        $response = $this->request(
            verb: 'POST', 
            url: "/v1/tokens/card",
            headers: [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer $access_token"
            ],
            body: json_encode($data)
        );

        return $response;
    }

    public function auth()
    {
        $response = $this->request(
            verb: 'POST', 
            url: "/auth/oauth/v2/token",
            headers: [
                "Content-Type" => "application/x-www-form-urlencoded",
                "Authorization" => "Basic ".base64_encode($this->client_id.":".$this->client_secret)
            ],
            body: http_build_query(['scope' => 'oob', 'grant_type' => 'client_credentials'])
        );

        return $response;
    }

    private function request($verb, $url, $headers=[], $body="")
    {
        $headers = array_to_header($headers); 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->endpoint.$url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($body)) curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $verb);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        // echo '<pre>';
        // print_r([
        //     'headers' => $headers,
        //     'url' => $this->endpoint.$url,
        //     'body' => $body
        // ]);
        // print_r($info);
        curl_close($curl);
        return $response;
    }
}