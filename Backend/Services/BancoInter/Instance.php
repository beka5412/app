<?php

namespace Backend\Services\BancoInter;

use Backend\Services\BancoInter\Request as InterRequest;

class Instance
{
    public static function instance()
    {
        return new InterRequest;
    }

    public static function pix(string $recipient, float $amount, ?string $description="")
    {
        $payload = [
            "valor" => $amount,
            "destinatario" => [
                "chave" => $recipient,
                "tipo" => "CHAVE"
            ],
            "descricao" => $description
        ];

        $instance = self::instance();
        $instance->access_token = $instance->token()->json?->access_token;

        return $instance->pix($payload);
    }
}