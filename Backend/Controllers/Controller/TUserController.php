<?php

namespace Backend\Controllers\Controller;

use Backend\App;
use Backend\Models\Administrator;
use Backend\Models\User;

trait TUserController
{
    public ?User $user;
    public ?Administrator $administrator;

    public string $subdomain = '';
    public string $domain = '';
    
    public function __construct(public App $application)
    {
        $this->user = user();
        $this->admin = admin();
        $this->subdomain = env('SUBDOMAIN_INDEX') ?: '';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }
}
