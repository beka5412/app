<?php

namespace Backend\Controllers\Webhooks\NoxPay;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Ezeksoft\PHPWriteLog\Log;

class InvalidSignatureException extends \Exception
{
    public function __construct($message = "Invalid signature.")
    {
        parent::__construct($message);
    }
}

class NoxController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    function validateWebhook($payload, $headerSignature, $secretKey): bool | \Exception
    {
        $data = $secretKey . $payload;
        $hashed = hash('sha256', $data, true);
        $signatureHash = base64_encode($hashed);

        if (!hash_equals($signatureHash, $headerSignature)) {
            throw new InvalidSignatureException();
        }

        return true;
    }

    public function wook(Request $request)
    {
        $payload = file_get_contents('php://input');
        $headerSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $secretKey = env('NOXPAY_SECRET_KEY');

        try {
            $logContent = [
                'timestamp' => date('Y-m-d H:i:s'),
                'payload' => $payload,
                'headerSignature' => $headerSignature,
            ];
            (new Log)->write(base_path('logs/nox_data.log'), json_encode($logContent, JSON_PRETTY_PRINT));

            if ($this->validateWebhook($payload, $headerSignature, $secretKey)) {

                return Response::json(['status' => 'success', 'message' => 'Webhook received'], '200 OK');
            }
        } catch (InvalidSignatureException $e) {
            http_response_code(403);
            echo $e->getMessage();
        }
    }
}