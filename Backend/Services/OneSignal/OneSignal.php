<?php

namespace Backend\Services\OneSignal;

class OneSignal
{
    public string $endpoint = "https://onesignal.com/api/v1";
    protected string $access_token = "";
    protected string $app_id = "";
    private string $title = "";
    private string $description = "";
    private array $data = [];
    private string $payload = "";

    /** @var array<string> */
    private array $external_user_ids = [];

    public function __construct(string $access_token="", string $app_id="")
    {
        $this->access_token = $access_token ?: env('ONESIGNAL_ACCESS_TOKEN');
        $this->app_id = $app_id ?: env('ONESIGNAL_APP_ID');
    }

    public function setAccessToken(string $access_token="") : OneSignal
    {
        $this->access_token = $access_token;
        return $this;
    }

    public function getAccessToken() : string
    {
        return $this->access_token;
    }

    public function setAppId(string $app_id="") : OneSignal
    {
        $this->app_id = $app_id;
        return $this;
    }

    public function getAppId() : string
    {
        return $this->app_id;
    }

    public function setTitle(string $title) : OneSignal
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setDescription(string $description="") : OneSignal
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setData(array $data=[]) : OneSignal
    {
        $this->data = $data;
        return $this;
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function hasData() : bool
    {
        return !empty($this->data);
    }

    public function setExternalUserIDs(array $external_user_ids=[]) : OneSignal
    {
        $this->external_user_ids = $external_user_ids;
        return $this;
    }

    public function addExternalUserID(string $user_id) : OneSignal
    {
        $this->external_user_ids[] = $user_id;
        return $this;
    }

    public function getExternalUserIDs() : array
    {
        return $this->external_user_ids;
    }
    
    public function pushNotification()
    {
        $payload = [
            "app_id" => $this->getAppId(),
            "headings" => [
                "en" => $this->getTitle()
            ],
            "contents" => [
                "en" => $this->getDescription()
            ],
            "name" => "INTERNAL_CAMPAIGN_NAME",
            "include_external_user_ids" => $this->getExternalUserIDs(),
            "channel_for_external_user_ids" => "push",
            "small_icon" => [
                "en" => "https://img.onesignal.com/tmp/a72b0207-7b2a-4b26-adc7-410ee9f88072/i66eLXZSSOKCEsRZTsMg_icon_mobile.png"
            ]
        ];
        
        if ($this->hasData()) $payload["data"] = $this->getData();

        $response = $this->request(
            verb: 'POST', 
            url: "/notifications",
            headers: [
                "Content-Type" => "application/json",
                "Authorization" => "Basic " . $this->getAccessToken(),
                "Accept" => "application/json"
            ],
            body: json_encode($payload)
        );

        return $response;
    }

    public function setPayload(string $payload) : OneSignal
    {
        $this->payload = $payload;
        return $this;
    }

    public function getPayload() : string
    {
        return $this->payload;
    }

    public function getUrl($url) : string
    {
        return $this->endpoint.$url;
    }

    private function request($verb, $url, $headers=[], $body="")
    {
        $this->setPayload($body);
        $headers = array_to_header($headers); 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->getUrl($url));
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
        curl_close($curl);

        // (new \Ezeksoft\PHPWriteLog\Log)->write(base_path("logs/onesignal.log"), [
        //     "endpoint" => $this->getUrl($url),
        //     "request" => [
        //         // "header" => $headers, 
        //         "body" => $body
        //     ], 
        //     "response" => $response
        // ]);

        return $response;
    }
}






// curl --request POST \
//      --url https://onesignal.com/api/v1/notifications \
//      --header 'Authorization: Basic M2M2ZmEwZmItYTlmMC00ZGFhLTgwZDYtM2JiYTc4OGFmYmNm' \
//      --header 'accept: application/json' \
//      --header 'content-type: application/json' \
//      --data '
//      {
//           "app_id": "86c9fd40-f373-4e5c-b690-c0e43c45b968",
//           "contents": {
//                "pt": "Sua comissão: R$285,00",
//                "en": "Your earn: R$ 285,00"
//           },
//           "name": "INTERNAL_CAMPAIGN_NAME",
//           "include_external_user_ids": ["quielbala@gmail.com"],
//           "channel_for_external_user_ids": "push",
//           "data": {"foo": "bar"},
//           "headings": {
//                "pt": "Compra aprovada",
//                "en": "Purchase approved!"
//           },
//           "small_icon": {
//                "pt": "https://img.onesignal.com/tmp/a72b0207-7b2a-4b26-adc7-410ee9f88072/i66eLXZSSOKCEsRZTsMg_icon_mobile.png",
//                "en": "https://img.onesignal.com/tmp/a72b0207-7b2a-4b26-adc7-410ee9f88072/i66eLXZSSOKCEsRZTsMg_icon_mobile.png"
//           }
//      }
// '