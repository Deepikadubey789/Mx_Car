@if (is_plugin_active('car-rentals') && ($currencies = get_all_currencies()) && $currencies->count() > 1)
    <div>{{ __('Currency') }}</div>
    <div class="currency-list-mobile">
        @php
            $currentCurrency = get_application_currency();
        @endphp
        @foreach ($currencies as $currency)
            <a class="currency-item text-sm-medium {{ $currency->getKey() === $currentCurrency->getKey() ? 'active' : '' }}" 
               href="{{ route('public.currency.switch', $currency->title) }}">
                <span class="currency-symbol">{{ $currency->symbol }}</span>
                {{ $currency->title }}
            </a>
        @endforeach
    </div>
@endif

