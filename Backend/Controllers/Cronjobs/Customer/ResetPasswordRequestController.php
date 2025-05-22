<?php

namespace Backend\Controllers\Cronjobs\Customer;
use Backend\Models\ResetPasswordRequest;

class ResetPasswordRequestController
{
    public function wook()
    {
        ResetPasswordRequest::where('expires_at', '<', today())->update(['is_available' => 0]);
    }
}