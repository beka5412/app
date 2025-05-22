<?php

namespace Backend\Controllers\Webhooks\NoxPay;

use Backend\App;
use Backend\Entities\Abstracts\NoxPay\NoxPayWebhookEvent;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Types\NoxPay\Events\Data\ENoxPayDataStatus;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
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
            return false;
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

            $body = json_decode($payload, true);

//            if (!$this->validateWebhook($payload, $headerSignature, $secretKey))
//            {
//                (new Log)->write(
//                    base_path('logs/nox_data.log'),
//                    json_encode([
//                        'error' => 'Webhook validation failed',
//                        'headerSignature' => $headerSignature,
//                        'expectedSecretKey' => $secretKey,
//                        'payload' => $payload,
//                        'timestamp' => now()->toDateTimeString(),
//                    ], JSON_PRETTY_PRINT)
//                );
//
//                return Response::json(
//                    new ResponseData([
//                        'status' => EResponseDataStatus::ERROR,
//                        'message' => 'Invalid NoxPay Token.'
//                    ]),
//                    new ResponseStatus('400 Bad Request')
//                );
//            }

            if($body['status'] === ENoxPayDataStatus::PAID->value)
            {
//                (new Log)->write(base_path('logs/nox_data.log'), json_encode(NoxPayWebhookEvent::invoice()->paid($request), JSON_PRETTY_PRINT));
                return NoxPayWebhookEvent::invoice()->paid($request);
            }

            return Response::json(
                new ResponseData([
                    'status' => EResponseDataStatus::SUCCESS,
                    'message' => 'No event captured'
                ]),
                new ResponseStatus('200 OK')
            );
        } catch (InvalidSignatureException $e) {
            http_response_code(403);
            echo $e->getMessage();
        }
    }
}