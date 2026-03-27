<?php

namespace Botble\CarRentals\Facades;

use Botble\CarRentals\Supports\InvoiceHelper as BaseInvoiceHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\CarRentals\Models\Invoice store(\Botble\CarRentals\Models\Booking $booking)
 * @method static \Botble\Base\Supports\Pdf makeInvoicePDF(\Botble\CarRentals\Models\Invoice $invoice)
 * @method static string generateInvoice(\Botble\CarRentals\Models\Invoice $invoice)
 * @method static \Illuminate\Http\Response|string|null downloadInvoice(\Botble\CarRentals\Models\Invoice $invoice)
 * @method static \Illuminate\Http\Response|string|null streamInvoice(\Botble\CarRentals\Models\Invoice $invoice)
 * @method static \Botble\CarRentals\Models\Invoice getDataForPreview()
 * @method static string getLanguageSupport()
 * @method static string getInvoiceTemplatePath()
 * @method static string getInvoiceTemplateCustomizedPath()
 * @method static string getInvoiceTemplate()
 * @method static array getVariables()
 * @method static array supportedDateFormats()
 * @method static array getDefaultInvoiceTemplatesFilter()
 * @method static string getInvoiceUrl(\Botble\CarRentals\Models\Invoice $invoice)
 * @method static string getInvoiceDownloadUrl(\Botble\CarRentals\Models\Invoice $invoice)
 *
 * @see \Botble\CarRentals\Supports\InvoiceHelper
 */
class InvoiceHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseInvoiceHelper::class;
    }
}
