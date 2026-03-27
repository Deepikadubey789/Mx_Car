<?php

namespace Botble\CarRentals\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Language;
use Botble\Base\Supports\Pdf;
use Botble\CarRentals\Enums\InvoiceStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\Invoice;
use Botble\CarRentals\Models\InvoiceItem;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InvoiceHelper
{
    public function store(Booking $booking): Invoice
    {
        if ($booking->invoice()->exists()) {
            return $booking->invoice;
        }

        $customer = $booking->customer;

        $invoiceData = [
            'reference_id' => $booking->getKey(),
            'reference_type' => Booking::class,
            'customer_id' => $customer->getKey() ? $customer->getKey() : 0,
            'customer_name' => $customer->getKey() ? $customer->name : $booking->customer_name,
            'customer_email' => $customer->getKey() ? $customer->email : $booking->customer_email,
            'customer_phone' => $customer->getKey() ? $customer->phone : $booking->customer_phone,
            'payment_id' => $booking->payment ? $booking->payment->id : null,
            'currency_id' => $booking->currency_id,
            'status' => InvoiceStatusEnum::COMPLETED,
            'paid_at' => Carbon::now(),
            'sub_total' => $booking->sub_total,
            'tax_amount' => $booking->tax_amount,
            'discount_amount' => $booking->coupon_amount ?: 0,
            'amount' => $booking->amount,
        ];

        if (is_plugin_active('payment')) {
            switch ($booking->payment->status) {
                case PaymentStatusEnum::COMPLETED:
                    $invoiceData['status'] = InvoiceStatusEnum::COMPLETED;

                    break;
                case PaymentStatusEnum::PENDING:
                    $invoiceData['status'] = InvoiceStatusEnum::PENDING;

                    break;
                case PaymentStatusEnum::FAILED:
                case PaymentStatusEnum::FRAUD:
                case PaymentStatusEnum::REFUNDED:
                case PaymentStatusEnum::REFUNDING:
                    $invoiceData['status'] = InvoiceStatusEnum::CANCELED;

                    break;
            }

            $invoiceData = array_merge($invoiceData, [
                'payment_id' => $booking->payment->id,
                'paid_at' => $booking->payment->status == PaymentStatusEnum::COMPLETED ? Carbon::now() : null,
            ]);
        }

        $invoice = new Invoice($invoiceData);
        $invoice->vendor_id = $booking->vendor_id;
        $invoice->created_at = $booking->created_at;

        $invoice->save();

        if ($car = $booking->car) {
            $invoice->items()->create([
                'name' => $car->car_name,
                'description' => null,
                'qty' => 1,
                'sub_total' => $car->price,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'amount' => $car->price,
            ]);
        }

        if ($services = $booking->services) {
            foreach ($services as $service) {
                $invoice->items()->create([
                    'name' => $service->name . ' (' . trans('plugins/car-rentals::car-rentals.service.name') . ')',
                    'description' => null,
                    'qty' => 1,
                    'sub_total' => $service->price,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'amount' => $service->price,
                ]);
            }
        }

        return $invoice;
    }

    public function makeInvoicePDF(Invoice $invoice): Pdf
    {
        return (new Pdf())
            ->templatePath($this->getInvoiceTemplatePath())
            ->destinationPath($this->getInvoiceTemplateCustomizedPath())
            ->supportLanguage($this->getLanguageSupport())
            ->paperSizeA4()
            ->data($this->getDataForInvoiceTemplate($invoice))
            ->twigExtensions([
                new TwigExtension(),
            ])
            ->setProcessingLibrary(get_car_rentals_setting('invoice_processing_library', 'dompdf'));
    }

    public function generateInvoice(Invoice $invoice): string
    {
        $storageDisk = Storage::disk('local');

        $invoiceFile = sprintf('car-rentals/invoices/invoice-%s.pdf', $invoice->code);

        $invoicePath = $storageDisk->path($invoiceFile);

        if ($storageDisk->exists($invoiceFile)) {
            return $invoicePath;
        }

        File::ensureDirectoryExists(dirname($invoicePath));

        $this->makeInvoicePDF($invoice)->save($invoicePath);

        return $invoicePath;
    }

    public function downloadInvoice(Invoice $invoice): Response|string|null
    {
        return $this->makeInvoicePDF($invoice)->download(sprintf('invoice-%s.pdf', $invoice->code));
    }

    public function streamInvoice(Invoice $invoice): Response|string|null
    {
        return $this->makeInvoicePDF($invoice)->stream(sprintf('invoice-%s.pdf', $invoice->code));
    }

    protected function getDataForInvoiceTemplate(Invoice $invoice): array
    {
        $booking = $invoice->reference;

        if ($booking) {
            $booking->load(['car', 'services']);
        }

        $invoiceData = [
            'invoice' => $invoice,
            'booking' => $booking,
            'logo_full_path' => RvMedia::getImageUrl(
                get_car_rentals_setting('company_logo_for_invoicing') ?: Theme::getLogo(),
            ),
            'site_title' => Theme::getSiteTitle(),
            'customer' => $invoice->customer,
            'settings' => [
                'using_custom_font_for_invoice' => get_car_rentals_setting('using_custom_font_for_invoice', false),
                'font_family' => get_car_rentals_setting('using_custom_font_for_invoice', 0) == 1
                    ? get_car_rentals_setting('invoice_font_family', '')
                    : 'DejaVu Sans',
                'enable_invoice_stamp' => get_car_rentals_setting('enable_invoice_stamp', true),
                'date_format' => get_car_rentals_setting('invoice_date_format', 'F d, Y'),
                'company_name_for_invoicing' => get_car_rentals_setting('company_name_for_invoicing') ?: theme_option(
                    'site_title'
                ),
                'company_address_for_invoicing' => get_car_rentals_setting('company_address_for_invoicing'),
                'company_email_for_invoicing' => get_car_rentals_setting('company_email_for_invoicing'),
                'company_phone_for_invoicing' => get_car_rentals_setting('company_phone_for_invoicing'),
                'car_rentals_invoice_footer' => apply_filters('car_rentals_invoice_footer', null, $invoice),
            ],
        ];

        if (is_plugin_active('payment')) {
            $payment = $invoice->payment ?: $booking->payment;

            if ($payment) {
                $invoiceData = [
                    ...$invoiceData,
                    'payment_method' => $payment->payment_channel->label(),
                    'payment_status' => $payment->status->label(),
                    'payment_id' => $payment->charge_id,
                    'payment_description' => ($payment->payment_channel == PaymentMethodEnum::BANK_TRANSFER && $payment->status == PaymentStatusEnum::PENDING)
                        ? BaseHelper::clean(get_payment_setting('description', $payment->payment_channel))
                        : null,
                ];
            }
        }

        $language = Language::getCurrentLocale();

        $invoiceData['html_attributes'] = trim(Html::attributes([
            'lang' => $language['locale'],
        ]));

        $invoiceData['body_attributes'] = trim(Html::attributes([
            'dir' => $language['is_rtl'] ? 'rtl' : 'ltr',
        ]));

        return $invoiceData;
    }

    public function getDataForPreview(): Invoice
    {
        $invoice = new Invoice([
            'code' => 'INV-1',
            'customer_name' => 'John Smith',
            'customer_email' => 'johnsmith@gmail.com',
            'customer_phone' => '0912345678',
            'status' => InvoiceStatusEnum::PENDING,
        ]);

        $items = [];

        foreach (range(1, 5) as $i) {
            $amount = rand(10, 1000);
            $qty = rand(1, 10);

            $items[] = new InvoiceItem([
                'name' => "Item $i",
                'description' => "Description of item $i",
                'amount' => $amount,
                'qty' => $qty,
            ]);

            $invoice->amount += $amount * $qty;
            $invoice->sub_total = $invoice->amount;
        }

        $payment = new Payment([
            'payment_channel' => PaymentMethodEnum::BANK_TRANSFER,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $customer = new Customer([
            'name' => 'John Smith',
            'email' => 'example@mail.com',
            'phone' => '0123456789',
        ]);

        $invoice->setRelation('payment', $payment);
        $invoice->setRelation('items', $items);
        $invoice->setRelation('customer', $customer);

        return $invoice;
    }

    public function getLanguageSupport(): string
    {
        $languageSupport = get_car_rentals_setting('invoice_language_support');

        if (! empty($languageSupport)) {
            return $languageSupport;
        }

        if (get_car_rentals_setting('invoice_support_arabic_language', false)) {
            return 'arabic';
        }

        if (get_car_rentals_setting('invoice_support_bangladesh_language', false)) {
            return 'bangladesh';
        }

        return '';
    }

    public function getInvoiceTemplatePath(): string
    {
        return plugin_path('car-rentals/resources/templates/invoice.tpl');
    }

    public function getInvoiceTemplateCustomizedPath(): string
    {
        return storage_path('app/templates/car-rentals/invoice.tpl');
    }

    public function getInvoiceTemplate(): string
    {
        return (new Pdf())
            ->supportLanguage($this->getLanguageSupport())
            ->twigExtensions([
                new TwigExtension(),
            ])
            ->getContent($this->getInvoiceTemplatePath(), $this->getInvoiceTemplateCustomizedPath());
    }

    public function getVariables(): array
    {
        return [
            'invoice.*' => __('Invoice information from database, ex: invoice.code, invoice.amount, ...'),
            'booking.*' => __('Booking information from database, ex: booking.booking_number, booking.note, ...'),
            'booking.car.*' => __('Car information from booking, ex: booking.car.car_name, booking.car.rental_start_date, ...'),
            'booking.services' => __('Services information from booking'),
            'account.*' => __('Bill payment user account information, ex: account.name, account.email, ...'),
            'payment_method' => __('Payment method'),
            'payment_status' => __('Payment status'),
            'payment_id' => __('Payment ID'),
            'payment_description' => __('Payment description'),
            'settings.using_custom_font_for_invoice' => __('Check site is using custom font for invoice or not'),
            'settings.font_family' => __('The font family of invoice template'),
            'settings.enable_invoice_stamp' => __('Check have enabled the invoice stamp'),
            'settings.company_name_for_invoicing' => __('The company name of invoice'),
            'settings.company_address_for_invoicing' => __('The company address of invoice'),
            'settings.company_email_for_invoicing' => __('The company email of invoice'),
            'settings.company_phone_for_invoicing' => __('The company phone number of invoice'),
        ];
    }

    public function supportedDateFormats(): array
    {
        $formats = [
            'M d, Y',
            'F j, Y',
            'F d, Y',
            'Y-m-d',
            'Y-M-d',
            'd-m-Y',
            'd-M-Y',
            'm/d/Y',
            'M/d/Y',
            'd/m/Y',
            'd/M/Y',
        ];

        return apply_filters('car_rentals_invoice_date_formats', $formats);
    }

    public function getDefaultInvoiceTemplatesFilter(): array
    {
        return [
            'booking' => [
                'label' => trans('plugins/car-rentals::invoice-template.booking_invoice_label'),
                'content' => fn () => $this->getInvoiceTemplate(),
                'variables' => fn () => $this->getVariables(),
                'customized_path' => $this->getInvoiceTemplateCustomizedPath(),
                'preview' => fn () => $this->streamInvoice($this->getDataForPreview()),
            ],
        ];
    }

    public function getInvoiceUrl(Invoice $invoice): string
    {
        return route('customer.invoices.generate', $invoice->id) . '?type=print';
    }

    public function getInvoiceDownloadUrl(Invoice $invoice): string
    {
        return route('customer.invoices.generate', $invoice->id);
    }
}
