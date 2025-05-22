<?php

namespace Backend\Services\Paylab;

class Request
{
    public $endpoint = 'https://alerts.paylab.com.br';

    public function request($verb, $url, $headers = [], $body = "", $timeout=0)
    {
        $endpoint = $this->endpoint . $url;
        $headers = array_to_header($headers);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($body)) curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $verb);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
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
