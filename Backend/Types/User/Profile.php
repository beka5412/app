<?php 

namespace Backend\Types\User;

class Profile
{
    public string $name;
    public string $email;
    public string $phone;
    public string $document;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->document = $data['document'];
    }
}
