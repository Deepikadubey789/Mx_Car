<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Facades\InvoiceHelper;
use Botble\CarRentals\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    public function getGenerateInvoice(Invoice $invoice, Request $request)
    {
        abort_if($invoice->vendor_id !== auth('customer')->id(), 403);

        if ($request->input('type') === 'print') {
            return InvoiceHelper::streamInvoice($invoice);
        }

        return InvoiceHelper::downloadInvoice($invoice);
    }
}
