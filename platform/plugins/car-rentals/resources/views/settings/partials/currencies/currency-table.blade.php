<div class="swatches-container">
    <div class="header">
        <div class="swatch-item">
            {{ trans('plugins/car-rentals::currency.code') }}
        </div>
        <div class="swatch-item">
            {{ trans('plugins/car-rentals::currency.symbol') }}
        </div>
        <div class="swatch-item swatch-exchange-rate">
            {{ trans('plugins/car-rentals::currency.exchange_rate') }}
        </div>
        <div class="swatch-is-default">
            {{ trans('plugins/car-rentals::currency.is_default') }}
        </div>
        <div class="swatch-advanced">
            {{ trans('plugins/car-rentals::currency.advanced') }}
        </div>
        <div class="remove-item">{{ trans('plugins/car-rentals::currency.remove') }}</div>
    </div>

    <ul class="swatches-list"></ul>

    <div class="d-flex justify-content-between w-100 align-items-center">
        <x-core::form.helper-text>
            {{ trans('plugins/car-rentals::currency.instruction') }}
        </x-core::form.helper-text>

        <a class="js-add-new-attribute" href="javascript:void(0)">
            {{ trans('plugins/car-rentals::currency.new_currency') }}
        </a>
    </div>
</div>
