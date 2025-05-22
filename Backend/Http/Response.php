<?php

namespace Backend\Http;

use Backend\Types\Response\ResponseStatus;
use Backend\Types\Response\ResponseData;

class Response
{
    public ResponseData|array $body;
    public ResponseStatus|string|null $status;

    public static function json(ResponseData|array $object, ResponseStatus|string|null $code = null, bool $enable_content_type = false)
    {
        if (gettype($code) === 'string' && $code)
            header("HTTP/1.1 $code");
        else if ($code instanceof ResponseStatus && $code->status)
            header("HTTP/1.1 $code->status");

        if ($enable_content_type)
            header("Content-Type: application/json");

        echo json_encode($object);

        $response = new self;
        $response->body = $object;
        $response->status = $code;
        return $response;
    }

    public static function redirect(mixed $url)
    {
        header("location: $url");
    }

    public static function htmlRedirect(mixed $url)
    {
        echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
        exit;
    }

    public static function jsRedirect(mixed $url)
    {
        echo "<script>document.location='$url'</script>";
        exit;
    }
}
