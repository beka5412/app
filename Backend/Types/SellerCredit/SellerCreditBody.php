<?php 

namespace Backend\Types\SellerCredit;

class SellerCreditBody
{
    public string|int $order_id;
    public string|int $user_id;
    public float $amount;
    public ?float $percent;
    public ?float $rate;
    public ESellerCredityBodyType $type;

    public function __construct(object|array $data)
    {
        if (gettype($data) === 'array') $data = (object) $data;

        $this->order_id = $data->order_id;
        $this->user_id = $data->user_id;
        $this->amount = $data->amount;
        if ($data->percent ?? false) $this->percent = $data->percent;
        if ($data->rate ?? false) $this->rate = $data->rate;
        if ($data->type ?? false) 
        {
            $status = strtoupper(gettype($data->type) === 'string' ? $data->type : $data->type->value);
            $this->type = ESellerCredityBodyType::cases()[array_search($status, array_column(ESellerCredityBodyType::cases(), "name"))];
        }
    }
}