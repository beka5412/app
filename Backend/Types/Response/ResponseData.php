<?php

namespace Backend\Types\Response;

class ResponseData
{
    public EResponseDataStatus $status;
    public ?string $message;

    public mixed $data;

    public function __construct(array $array)
    {
        $status = strtoupper(gettype($array['status']) === 'string' ? $array['status'] : $array['status']->value);
        $this->status = EResponseDataStatus::cases()[array_search($status, array_column(EResponseDataStatus::cases(), "name"))];

        if ($array['message'] ?? null)
            $this->message = $array['message'];

        if ($array['data'] ?? null)
            $this->data = $array['data'];
    }
}
