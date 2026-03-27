<?php

namespace Botble\CarRentals\Forms\Settings;

use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\GoogleFontsField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\CarRentals\Http\Requests\Settings\InvoiceSettingRequest;
use Botble\CarRentals\Supports\InvoiceHelper;
use Botble\Setting\Forms\SettingForm;
use Botble\Theme\Facades\Theme;

class InvoiceSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/car-rentals::settings.invoice.title'))
            ->setSectionDescription(trans('plugins/car-rentals::settings.invoice.description'))
            ->setValidatorClass(InvoiceSettingRequest::class)
            ->addCustomField('googleFonts', GoogleFontsField::class)
            ->add(
                'company_name_for_invoicing',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.company_name'))
                    ->value(get_car_rentals_setting('company_name_for_invoicing', theme_option('site_title')))
                    ->placeholder(trans('plugins/car-rentals::settings.invoice.forms.company_name_placeholder'))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.company_name_helper'))
            )
            ->add(
                'company_address_for_invoicing',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.company_address'))
                    ->value(get_car_rentals_setting('company_address_for_invoicing'))
                    ->placeholder(trans('plugins/car-rentals::settings.invoice.forms.company_address_placeholder'))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.company_address_helper'))
            )
            ->add(
                'company_email_for_invoicing',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.company_email'))
                    ->value(get_car_rentals_setting('company_email_for_invoicing', get_admin_email()->first()))
                    ->placeholder(trans('plugins/car-rentals::settings.invoice.forms.company_email_placeholder'))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.company_email_helper'))
            )
            ->add(
                'company_phone_for_invoicing',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.company_phone'))
                    ->value(get_car_rentals_setting('company_phone_for_invoicing'))
                    ->placeholder(trans('plugins/car-rentals::settings.invoice.forms.company_phone_placeholder'))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.company_phone_helper'))
            )
            ->add(
                'company_logo_for_invoicing',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.company_logo'))
                    ->value(get_car_rentals_setting('company_logo_for_invoicing') ?: Theme::getLogo())
                    ->allowThumb(false)
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.company_logo_helper'))
            )
            ->add(
                'using_custom_font_for_invoice',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.using_custom_font_for_invoice'))
                    ->value(get_car_rentals_setting('using_custom_font_for_invoice', false))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.using_custom_font_for_invoice_helper'))
                    ->attributes([
                        'data-bb-toggle' => 'collapse',
                        'data-bb-target' => '.custom-font-settings',
                    ])
            )
            ->add(
                'open_fieldset_custom_font_settings',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(sprintf(
                        '<fieldset class="form-fieldset custom-font-settings" style="display: %s;" data-bb-value="1">',
                        get_car_rentals_setting('using_custom_font_for_invoice', false) ? 'block' : 'none'
                    ))
            )
            ->add('invoice_font_family', 'googleFonts', [
                'label' => trans('plugins/car-rentals::settings.invoice.forms.invoice_font_family'),
                'selected' => get_car_rentals_setting('invoice_font_family'),
                'help_block' => [
                    'text' => trans('plugins/car-rentals::settings.invoice.forms.invoice_font_family_helper'),
                ],
            ])
            ->add(
                'close_fieldset_custom_font_settings',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('</fieldset>')
            )
            ->add(
                'invoice_support_arabic_language',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.invoice_support_arabic_language'))
                    ->value(get_car_rentals_setting('invoice_support_arabic_language', false))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.invoice_support_arabic_language_helper'))
            )
            ->add(
                'enable_invoice_stamp',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.enable_invoice_stamp'))
                    ->value(get_car_rentals_setting('enable_invoice_stamp', true))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.enable_invoice_stamp_helper'))
            )
            ->add(
                'invoice_code_prefix',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.invoice_code_prefix'))
                    ->value(get_car_rentals_setting('invoice_code_prefix', 'INV-'))
                    ->placeholder(trans('plugins/car-rentals::settings.invoice.forms.invoice_code_prefix_placeholder'))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.invoice_code_prefix_helper'))
            )
            ->add(
                'invoice_language_support',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.add_language_support'))
                    ->choices([
                        'default' => trans('plugins/car-rentals::settings.invoice.forms.only_latin_languages'),
                        'arabic' => 'Arabic',
                        'bangladesh' => 'Bangladesh',
                        'chinese' => 'Chinese',
                    ])
                    ->defaultValue('default')
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.add_language_support_helper'))
                    ->when(
                        (new InvoiceHelper())->getLanguageSupport(),
                        function (RadioFieldOption $option, string $language): void {
                            $option->selected($language);
                        }
                    )
                    ->colspan(6)
            )
            ->add(
                'invoice_processing_library',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.invoice_processing_library'))
                    ->choices([
                        'dompdf' => 'DomPDF',
                        'mpdf' => 'mPDF',
                    ])
                    ->defaultValue('dompdf')
                    ->selected(get_car_rentals_setting('invoice_processing_library', 'dompdf'))
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.invoice_processing_library_helper'))
                    ->colspan(6)
            )
            ->add(
                'invoice_date_format',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.invoice.forms.date_format'))
                    ->choices(array_combine((new InvoiceHelper())->supportedDateFormats(), (new InvoiceHelper())->supportedDateFormats()))
                    ->selected(get_car_rentals_setting('invoice_date_format', 'F d, Y'))
                    ->searchable()
                    ->helperText(trans('plugins/car-rentals::settings.invoice.forms.date_format_helper'))
                    ->colspan(6)
            );
    }
}
