class Currencies {
    constructor() {
        this.template = $('#currency_template').html()
        this.totalItem = 0
        this.deletedItems = []
        this.initData()
        this.handleForm()
        this.updateCurrency()
        this.clearCacheRates()
        this.changeOptionUsingExchangeRateCurrencyFormAPI()
        this.handleAdvancedToggle()
    }

    initData() {
        const currencies = $.parseJSON($('#currencies').html())
        $.each(currencies, (index, currency) => {
            const currencyItem = this.template
                .replace(/__id__/gi, currency.id)
                .replace(/__position__/gi, currency.order)
                .replace(/__isPrefixSymbolChecked__/gi, currency.is_prefix_symbol == 1 ? 'selected' : '')
                .replace(/__notIsPrefixSymbolChecked__/gi, currency.is_prefix_symbol == 0 ? 'selected' : '')
                .replace(/__isDefaultChecked__/gi, currency.is_default == 1 ? 'checked' : '')
                .replace(/__westernFormatChecked__/gi, (currency.number_format_style || 'western') == 'western' ? 'selected' : '')
                .replace(/__indianFormatChecked__/gi, currency.number_format_style == 'indian' ? 'selected' : '')
                .replace(/__spaceBetweenPriceAndCurrencyChecked__/gi, currency.space_between_price_and_currency == 1 ? 'checked' : '')
                .replace(/__title__/gi, currency.title)
                .replace(/__decimals__/gi, currency.decimals)
                .replace(/__exchangeRate__/gi, currency.exchange_rate)
                .replace(/__symbol__/gi, currency.symbol)

            $('.swatches-container .swatches-list').append(currencyItem)
            this.totalItem++
        })
    }

    addNewAttribute() {
        const currencyItem = this.template
            .replace(/__id__/gi, 0)
            .replace(/__position__/gi, this.totalItem)
            .replace(/__isPrefixSymbolChecked__/gi, '')
            .replace(/__notIsPrefixSymbolChecked__/gi, '')
            .replace(/__isDefaultChecked__/gi, this.totalItem == 0 ? 'checked' : '')
            .replace(/__westernFormatChecked__/gi, 'selected')
            .replace(/__indianFormatChecked__/gi, '')
            .replace(/__spaceBetweenPriceAndCurrencyChecked__/gi, '')
            .replace(/__title__/gi, '')
            .replace(/__decimals__/gi, 0)
            .replace(/__exchangeRate__/gi, 1)
            .replace(/__symbol__/gi, '')

        $('.swatches-container .swatches-list').append(currencyItem)
        this.totalItem++
    }

    exportData() {
        const data = []
        $('.swatches-container .swatches-list li.currency-item').each((index, item) => {
            const $item = $(item)
            data.push({
                id: $item.data('id'),
                is_default: $item.find('.currency-row [data-type=is_default] input[type=radio]').is(':checked') ? 1 : 0,
                order: $item.index(),
                title: $item.find('.currency-row [data-type=title] input').val(),
                symbol: $item.find('.currency-row [data-type=symbol] input').val(),
                decimals: $item.find('.currency-advanced-settings [data-type=decimals]').val(),
                number_format_style: $item.find('.currency-advanced-settings [data-type=number_format_style]').val(),
                space_between_price_and_currency: $item.find('.currency-advanced-settings [data-type=space_between_price_and_currency]').is(':checked') ? 1 : 0,
                exchange_rate: $item.find('.currency-row [data-type=exchange_rate] input').val(),
                is_prefix_symbol: $item.find('.currency-advanced-settings [data-type=is_prefix_symbol]').val(),
            })
        })

        return data
    }

    handleForm() {
        $('.swatches-container .swatches-list').sortable()

        $('body')
            .on('submit', '.currency-setting-form', () => {
                const currencies = this.exportData()
                $('#currencies').val(JSON.stringify(currencies))
                $('#deleted_currencies').val(JSON.stringify(this.deletedItems))
            })
            .on('click', '.js-add-new-attribute', (event) => {
                event.preventDefault()
                this.addNewAttribute()
            })
            .on('click', '.swatches-container .swatches-list li .remove-item a', (event) => {
                event.preventDefault()
                const $item = $(event.currentTarget).closest('li')
                this.deletedItems.push($item.data('id'))
                $item.remove()
            })
    }

    updateCurrency() {
        $(document).on('click', '#btn-update-currencies', (event) => {
            event.preventDefault()
            const $btn = $(event.currentTarget)
            const form = $('.currency-setting-form')

            $httpClient
                .make()
                .post(form.prop('action'), form.serialize())
                .then(({ data }) => {
                    if (data.error) {
                        Botble.showError(data.message)
                    } else {
                        $httpClient
                            .make()
                            .withButtonLoading($btn)
                            .withLoading(form.find('.swatches-container'))
                            .post($btn.data('url'))
                            .then(({ data }) => {
                                if (!data.error) {
                                    Botble.showNotice('success', data.message)
                                    const template = $('#currency_template').html()
                                    let html = ''
                                    $.each(data.data, (index, item) => {
                                        html += template
                                            .replace(/__id__/gi, item.id)
                                            .replace(/__position__/gi, item.order)
                                            .replace(/__isPrefixSymbolChecked__/gi, item.is_prefix_symbol == 1 ? 'selected' : '')
                                            .replace(/__notIsPrefixSymbolChecked__/gi, item.is_prefix_symbol == 0 ? 'selected' : '')
                                            .replace(/__isDefaultChecked__/gi, item.is_default == 1 ? 'checked' : '')
                                            .replace(/__westernFormatChecked__/gi, (item.number_format_style || 'western') == 'western' ? 'selected' : '')
                                            .replace(/__indianFormatChecked__/gi, item.number_format_style == 'indian' ? 'selected' : '')
                                            .replace(/__spaceBetweenPriceAndCurrencyChecked__/gi, item.space_between_price_and_currency == 1 ? 'checked' : '')
                                            .replace(/__title__/gi, item.title)
                                            .replace(/__decimals__/gi, item.decimals)
                                            .replace(/__exchangeRate__/gi, item.exchange_rate)
                                            .replace(/__symbol__/gi, item.symbol)
                                    })
                                    setTimeout(() => {
                                        $('.swatches-container .swatches-list').html(html)
                                    }, 1000)
                                } else {
                                    Botble.showNotice('error', data.message)
                                }
                            })
                    }
                })
        })
    }

    clearCacheRates() {
        $(document).on('click', '#btn-clear-cache-rates', (event) => {
            event.preventDefault()
            const $btn = $(event.currentTarget)

            $httpClient
                .make()
                .withButtonLoading($btn)
                .post($btn.data('url'))
                .then(({ data }) => {
                    if (!data.error) {
                        Botble.showSuccess(data.message)
                    } else {
                        Botble.showError(data.message)
                    }
                })
        })
    }

    changeOptionUsingExchangeRateCurrencyFormAPI() {
        $(document).on('change', 'input[name="use_exchange_rate_from_api"]', (event) => {
            event.preventDefault()
            const inputExchangeRate = $('.swatch-exchange-rate').find('.input-exchange-rate')

            if (event.target.checked) {
                inputExchangeRate.prop('disabled', true)
            } else {
                inputExchangeRate.prop('disabled', false)
            }
        })
    }

    handleAdvancedToggle() {
        $(document).on('click', '.toggle-advanced', (event) => {
            event.preventDefault()
            const $button = $(event.currentTarget)
            const $currencyItem = $button.closest('.currency-item')
            const $advancedSettings = $currencyItem.find('.currency-advanced-settings')

            $advancedSettings.slideToggle(300, function() {
                if ($advancedSettings.is(':visible')) {
                    $button.addClass('active')
                } else {
                    $button.removeClass('active')
                }
            })
        })
    }
}

$(() => new Currencies())