<?php
/*
curl --request POST \
--url https://manage.runcloud.io/api/v2/servers/239001/webapps/1150394/domains \
-u zOpejDbLYn1jRxHIPfk1mXaZrCcmoNAHuRiBkRm5rj8S:dw4O6jOeJ8YbauQba5xnp0IAJN7QIESy77Cu3M8qmUVJ0MplmrzvodnldihFPgNa \
--header 'accept: application/json' \
--header 'content-type: application/json' \
--data-raw '{
    "name"         : "purchase.imembros.com",
    "www"          : true,
    "redirection"  : "none",
    "type"         : "alias"
}'
*/

namespace Backend\Services\RunCloud;

class RunCloud
{
    public $endpoint = 'https://manage.runcloud.io/api/v2';

    public $api_key = "";
    public $api_secret = "";
    public $server_id = "";
    public $app_id = "";

    public function setID($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    public function __construct(?string $api_key="", ?string $api_secret="")
    {        
        if (empty($api_key) || empty($api_secret)) 
        {
            $this->setID(env('RUNCLOUD_API_KEY'), env('RUNCLOUD_API_SECRET'));
            $this->server_id = env('RUNCLOUD_SERVER_ID');
            $this->app_id = env('RUNCLOUD_APP_ID');
        }
    }

    public function addDomain($domain)
    {
        return $this->request(
            verb: 'POST',
            url: "/servers/$this->server_id/webapps/$this->app_id/domains",
            headers: ["Content-Type" => "application/json", "Accept" => "application/json"],
            body: json_encode([
                "name" => $domain,
                "www" => false,
                "redirection" => "none",
                "type" => "alias",
            ])
        );
    }

    private function request($verb, $url, $headers=[], $body="")
    {
        $headers = array_to_header($headers); 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->endpoint.$url);
        curl_setopt($curl, CURLOPT_USERPWD, $this->api_key.":".$this->api_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($body)) curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $verb);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}