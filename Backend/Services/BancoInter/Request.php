<?php

namespace Backend\Services\BancoInter;

class Request
{
    public $endpoint = '';
    public $access_token = '';
    public $client_id = '';
    public $client_secret = '';
    public $ssl_key = '';
    public $ssl_crt = '';

    public function __construct()
    {
        $this->endpoint = env('BANCOINTER_ENDPOINT');
        $this->ssl_key = env('BANCOINTER_SSL_KEY');
        $this->ssl_crt = env('BANCOINTER_SSL_CRT');
        $this->client_id = env('BANCOINTER_CLIENT_ID');
        $this->client_secret = env('BANCOINTER_CLIENT_SECRET');
    }

    public function token()
    {
        $response = $this->request(
            verb: 'POST', 
            url: "/oauth/v2/token",
            headers: [
                "Content-Type" => "application/x-www-form-urlencoded"
            ],
            body: http_build_query([
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "grant_type" => "client_credentials",
                "scope" => "pagamento-pix.write pagamento-pix.read pix.read pix.write extrato.read webhook.read webhook-banking.write webhook-banking.read"
            ])
        );

        return $response;
    }

    public function pix($data)
    {
        $response = $this->request(
            verb: 'POST', 
            url: "/banking/v2/pix",
            headers: [
                "Authorization" => "Bearer $this->access_token",
                "Content-Type" => "application/json"
            ],
            body: json_encode($data)
        );

        return $response;
    }

    public function pixWebhook(string $url)
    {        
        $response = $this->request(
            verb: 'PUT', 
            url: "/banking/v2/webhooks/pix-pagamento",
            headers: [
                "Authorization" => "Bearer $this->access_token",
                "Content-Type" => "application/json"
            ],
            body: json_encode(["webhookUrl" => $url])
        );

        return $response;
    }

    private function request($verb, $url, $headers=[], $body="")
    {
        $endpoint = $this->endpoint.$url;
        $headers = array_to_header($headers); 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_SSLCERT, abs_path(env('BANCOINTER_SSL_CRT')));
        curl_setopt($curl, CURLOPT_SSLKEY, abs_path(env('BANCOINTER_SSL_KEY')));
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
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $time = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        curl_close($curl);

        return (object) [
            'verb' => $verb,
            "url" => $endpoint,
            "errno" => $errno,
            "error" => $error,
            "status_code" => $status_code,
            "time" => $time,
            "body" => $response,
            "json" => json_decode($response ?? '{}'),
        ];
    }
}