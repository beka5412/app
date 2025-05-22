<?php

namespace Backend\Types\SellerCredit;

class SellerCreditBodyWhere
{
    public string|int|null $order_id;
    public string|int|null $user_id;
    public ?float $amount;
    public ?float $percent;
    public ?float $rate;
    public ?ESellerCredityBodyType $type;

    public function __construct(object|array $data)
    {
        if (gettype($data) === 'array') $data = (object) $data;

        if ($data->order_id ?? false) $this->order_id = $data->order_id;
        if ($data->user_id ?? false) $this->user_id = $data->user_id;
        if ($data->amount ?? false) $this->amount = $data->amount;
        if ($data->percent ?? false) $this->percent = $data->percent;
        if ($data->rate ?? false) $this->rate = $data->rate;
        if ($data->type ?? false) $this->type = $data->type;
    }
}
