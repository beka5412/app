<?php

namespace Backend\Entities\Abstracts\NoxPay\Events\Invoice;

use Backend\Http\Request;
use Ezeksoft\PHPWriteLog\Log;

class Invoice
{
    public function paid(Request $request)
    {
        (new Log)->write(base_path('logs/nox_data.log'), json_encode($request, JSON_PRETTY_PRINT));
        return (new InvoicePaid)->response($request);
    }
}
