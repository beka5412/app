<?php 

namespace Backend\Types\User;

class Address
{
    public string $zipcode;
    public string $street;
    public string $number;
    public string $neighborhood;
    public string $city;
    public string $state;

    public function __construct(array $data)
    {
        $this->zipcode = $data['zipcode'];
        $this->street = $data['street'];
        $this->number = $data['number'];
        $this->neighborhood = $data['neighborhood'];
        $this->city = $data['city'];
        $this->state = $data['state'];
    }
}
