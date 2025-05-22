<?php

namespace Backend\Controllers\Webhooks\GetNet;

use Backend\App;
use Backend\Http\Request;
use Backend\Entities\Abstracts\PaymentWebhook;
use Ezeksoft\PHPWriteLog\Log;

class GetNetController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function wook()
    {
        $json = file_get_contents('php://input');
        $object = json_decode($json); 
        $now = date('Y-m-d H:i:s');

        (new Log)->write(base_path('logs/getnet.log'), $json);
    }
}