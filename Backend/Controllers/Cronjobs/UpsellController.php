<?php 

namespace Backend\Controllers\Cronjobs;

use Backend\Models\Customer;

class UpsellController
{
    public function token_delete()
    {
        Customer::where('upsell_pin_at', '<=', today())->update(['upsell_pin' => null]);
    }
}