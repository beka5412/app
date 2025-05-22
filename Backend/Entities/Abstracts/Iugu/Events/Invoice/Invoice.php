<?php

namespace Backend\Entities\Abstracts\Iugu\Events\Invoice;

use Backend\Http\Request;

class Invoice
{
    public function paid(Request $request)
    {
        return (new InvoicePaid)->response($request);
    }

    public function refunded(Request $request)
    {
        return (new InvoiceRefunded)->response($request);
    }

    public function chargeback(Request $request)
    {
        return (new InvoiceChargeback)->response($request);
    }
}
