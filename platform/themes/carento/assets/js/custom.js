;(function ($) {
    ;('use strict')

    $(document).ready(function () {
        const initLocationAutocomplete = ($context = $(document)) => {
            if (!$.fn.locationAutocomplete) {
                return
            }

            const $scope = $context && $context.length ? $context : $(document)
            const $targets =
                $scope[0] === document
                    ? $('[data-bb-toggle="search-suggestion"]')
                    : $scope.find('[data-bb-toggle="search-suggestion"]')

            if ($targets.length > 0) {
                $targets.locationAutocomplete()
            }
        }

        initLocationAutocomplete()

        if (typeof $.fn.daterangepicker !== 'undefined' && $('#date-range-picker').length > 0) {
            const dateRangePicker = $('#date-range-picker')
            const startDateInput = $('#input-start-date')
            const endDateInput = $('#input-end-date')

            let startDate = startDateInput.val() || moment().format('YYYY-MM-DD')
            let endDate = endDateInput.val() || moment().add(1, 'days').format('YYYY-MM-DD')

            const siteLocale = window.siteConfig?.locale || 'en'
            const dateRangeSeparator = window.siteConfig?.dateRangeSeparator || ' to '

            if (typeof moment !== 'undefined' && moment.locale) {
                moment.locale(siteLocale)
            }

            const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1)

            dateRangePicker.daterangepicker({
                startDate: moment(startDate),
                endDate: moment(endDate),
                minDate: moment(),
                maxYear: moment().year() + 2,
                autoApply: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: dateRangeSeparator,
                    daysOfWeek: moment.weekdaysMin().map(capitalize),
                    monthNames: moment.months().map(capitalize),
                    firstDay: moment.localeData().firstDayOfWeek(),
                },
                opens: 'left',
                drops: 'down',
            })

            dateRangePicker.on('apply.daterangepicker', function (ev, picker) {
                startDateInput.val(picker.startDate.format('YYYY-MM-DD'))
                endDateInput.val(picker.endDate.format('YYYY-MM-DD'))
            })

            dateRangePicker.val(startDate + dateRangeSeparator + endDate)
        }

        $('#js-box-search-advance .box-top-search .btn-click').on('click', function () {
            $('#js-box-search-advance .box-top-search input').val($(this).data('tab'))
        })
        $('#js-box-search-advance #pick-up-location .dropdown-menu .dropdown-item').on('click', function (e) {
            $('#js-box-search-advance #pick-up-location .location-search span').text($(this).text())
            $('#js-box-search-advance #pick-up-location input').val($(this).data('id'))
        })
        $('#js-box-search-advance #drop-off-location .dropdown-menu .dropdown-item').on('click', function (e) {
            $('#js-box-search-advance #drop-off-location input').val($(this).data('id'))
        })

        $('.car-detail-share .dropdown-item').on('click', function () {
            if ($(this).closest('.dropdown-item')) {
                window.open($(this).closest('.dropdown-item').attr('href'), '_blank')
            }
        })

        $('.dropdown-login .dropdown-item').on('click', function () {
            if ($(this).closest('.dropdown-item')) {
                window.location.href = $(this).closest('.dropdown-item').attr('href')
            }
        })

        try {
            new Tobii()
        } catch (err) {
            console.log(err)
        }

        Number.prototype.format_price = function (n, x) {
            let currencies = window.currencies || {}
            if (!n) {
                n = window.currencies.number_after_dot !== undefined ? window.currencies.number_after_dot : 2
            }
            let re = '\\d(?=(\\d{' + (x || 3) + '})+$)'
            let priceUnit = ''
            let price = this
            if (window.currencies.show_symbol_or_title) {
                priceUnit = window.currencies.symbol || window.currencies.title
            }
            if (window.currencies.display_big_money) {
                if (price >= 1000000 && price < 1000000000) {
                    price = price / 1000000
                    priceUnit = window.currencies.million + (priceUnit ? ' ' + priceUnit : '')
                } else if (price >= 1000000000) {
                    price = price / 1000000000
                    priceUnit = window.currencies.billion + (priceUnit ? ' ' + priceUnit : '')
                }
            }
            price = price.toFixed(Math.max(0, ~~n))
            price = price.toString().split('.')
            price =
                price[0].toString().replace(new RegExp(re, 'g'), '$&' + window.currencies.thousands_separator) +
                (price[1] ? currencies.decimal_separator + price[1] : '')
            if (currencies.show_symbol_or_title) {
                if (currencies.is_prefix_symbol) {
                    price = priceUnit + price
                } else {
                    price = price + priceUnit
                }
            }
            return price
        }

        let submitForm = (e, element = null) => {
            let $this = ''

            if (element) {
                $this = element
            } else if (e) {
                $this = $(e.currentTarget)
            }

            let $form = $this.closest('form')
            if (!$form.length && $this.prop('form')) {
                $form = $($this.prop('form'))
            }

            if ($form.length) {
                $form.trigger('submit')
            }
        }

        const handleError = (data) => {
            if (typeof data.errors !== 'undefined' && data.errors.length) {
                handleValidationError(data.errors)
            } else if (typeof data.responseJSON !== 'undefined') {
                if (typeof data.responseJSON.errors !== 'undefined') {
                    if (data.status === 422) {
                        handleValidationError(data.responseJSON.errors)
                    }
                } else if (typeof data.responseJSON.message !== 'undefined') {
                    Theme.showError(data.responseJSON.message)
                } else {
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            Theme.showError(item)
                        })
                    })
                }
            } else {
                Theme.showError(data.statusText)
            }
        }

        const handleValidationError = (errors) => {
            let message = ''
            $.each(errors, (index, item) => {
                if (message !== '') {
                    message += '<br />'
                }
                message += item
            })
            Theme.showError(message)
        }

        window.showAlert = (messageType, title, message) => {
            if (messageType && message !== '') {
                let alertId = Math.floor(Math.random() * 1000)

                let type = null
                let colorType = null
                title = title || 'Alert'

                switch (messageType) {
                    case 'alert-success':
                        type = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="45px">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>`

                        colorType = 'success'
                        break

                    case 'status':
                        type = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="45px">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>`

                        colorType = 'success'
                        break

                    case 'alert-danger':
                        type = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="45px">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>`

                        colorType = 'error'
                        break
                }

                let html = `
                <div class="toast ${colorType}" id="${alertId}">
                    <div class="outer-container">
                        ${type}
                    </div>
                    <div class="inner-container">
                        <p>${title}</p>
                        <p>${message}</p>
                    </div>
                    <a class="close-toast" >&times;</a>
                </div>
            `

                $('#alert-container')
                    .append(html)
                    .ready(() => {
                        window.setTimeout(() => {
                            $(`#alert-container #${alertId}`).remove()
                        }, 6000)
                    })

                $('#alert-container').on('click', '.close-toast', function (event) {
                    event.preventDefault()
                    $(this).closest('.toast').remove()
                })
            }
        }

        const loading = $('.loading-ajax')

        loading.hide()

        const initUiSlider = (element, forceReinit = false) => {
            if (!element || element.length === 0) return

            if (element[0] && element[0].noUiSlider) {
                if (forceReinit) {
                    element[0].noUiSlider.destroy()
                    element[0].noUiSlider = null
                } else {
                    return
                }
            }

            let carMaxRentalRate = parseInt(element.data('maxRentalRateRange'))

            if (!carMaxRentalRate || carMaxRentalRate <= 0) {
                console.warn('Invalid max rental rate for price slider')
                return
            }

            if (element.length > 0) {
                let moneyFormat = wNumb({
                    decimals: 0,
                    thousand: ',',
                    prefix: '',
                })

                const $form = $('#cars-filter-form')
                const $wrapper = element.closest('.price-slider-wrapper')

                const rentalRateFrom = parseInt($form.find('input[name="rental_rate_from"]').val()) || 0
                const rentalRateTo = parseInt($form.find('input[name="rental_rate_to"]').val()) || carMaxRentalRate

                noUiSlider.create(element[0], {
                    start: [rentalRateFrom, rentalRateTo],
                    tooltips: [wNumb({ decimals: 0 }), wNumb({ decimals: 0 })],
                    step: 1,
                    range: {
                        min: 0,
                        max: carMaxRentalRate,
                    },
                    format: moneyFormat,
                    connect: true,
                })

                const rate = $(element[0]).attr('data-currency-rate') || 1

                element[0].noUiSlider.on('update', function (values, handle) {
                    $form.find('input[name="rental_rate_from"]').val(moneyFormat.from(values[0]))
                    $form.find('input[name="rental_rate_to"]').val(moneyFormat.from(values[1]))

                    const fromValue = parseInt(values[0]) * parseFloat(rate)
                    const toValue = parseInt(values[1]) * parseFloat(rate)

                    $wrapper.find('.rental-rate-from').text(fromValue.toLocaleString())
                    $wrapper.find('.rental-rate-to').text(toValue.toLocaleString())
                })

                let priceTimeout
                element[0].noUiSlider.on('change', function (values, handle) {
                    clearTimeout(priceTimeout)
                    priceTimeout = setTimeout(function () {
                        const $form = $('#cars-filter-form')
                        if ($form.length) {
                            $form.trigger('submit')
                        }
                    }, 500)
                })
            }
        }

        const initHorsepowerSlider = (element, forceReinit = false) => {
            if (!element || element.length === 0) return

            if (element[0] && element[0].noUiSlider) {
                if (forceReinit) {
                    element[0].noUiSlider.destroy()
                    element[0].noUiSlider = null
                } else {
                    return
                }
            }

            let carMaxHorsepower = parseInt(element.data('maxHorsepowerRange'))

            if (!carMaxHorsepower || carMaxHorsepower <= 0) {
                console.warn('Invalid max horsepower for horsepower slider')
                return
            }

            if (element.length > 0) {
                const $form = $('#cars-filter-form')
                const $wrapper = element.closest('.horsepower-slider-wrapper')

                let horsepowerFormat = wNumb({
                    decimals: 0,
                    thousand: ',',
                    prefix: '',
                    suffix: ' HP',
                })
                const horsepowerTo = parseInt($form.find('input[name="horsepower_to"]').val()) || carMaxHorsepower

                noUiSlider.create(element[0], {
                    start: horsepowerTo,
                    tooltips: [wNumb({ decimals: 0, suffix: ' HP' })],
                    step: 1,
                    range: {
                        min: 0,
                        max: carMaxHorsepower,
                    },
                    format: horsepowerFormat,
                    connect: 'lower',
                })

                element[0].noUiSlider.on('update', function (values, handle) {
                    $form.find('input[name="horsepower_from"]').val(0)

                    const horsepowerValue = values[0] || '0 HP'
                    const numericValue =
                        typeof horsepowerValue === 'string' ? horsepowerValue.replace(' HP', '') : horsepowerValue
                    $form.find('input[name="horsepower_to"]').val(numericValue)

                    $wrapper.find('.horsepower-from').text('0 HP')
                    $wrapper.find('.horsepower-to').text(horsepowerValue)
                })

                let horsepowerTimeout
                element[0].noUiSlider.on('change', function (values, handle) {
                    clearTimeout(horsepowerTimeout)
                    horsepowerTimeout = setTimeout(function () {
                        const $form = $('#cars-filter-form')
                        if ($form.length) {
                            $form.trigger('submit')
                        }
                    }, 500)
                })
            }
        }

        const initSelect2 = (element) => {
            element.select2({
                minimumInputLength: 0,
                placeholder: element.data('placeholder'),
                tags: true,
                ajax: {
                    url: element.data('url') || `${window.siteUrl}/ajax/locations`,
                    dataType: 'json',
                    delay: 500,
                    type: 'GET',
                    data: function (params) {
                        return {
                            k: params.term,
                            page: params.page || 1,
                            type: element.data('location-type'),
                        }
                    },
                    processResults: function (data, params) {
                        return {
                            results: $.map(data.data[0], function (item) {
                                return {
                                    text: item.name,
                                    id: item.name,
                                    data: item,
                                }
                            }),
                            pagination: {
                                more: params.page * 10 < data.total,
                            },
                        }
                    },
                },
            })
        }

        const initYearSlider = (element, forceReinit = false) => {
            if (!element || element.length === 0) return

            if (element[0] && element[0].noUiSlider) {
                if (forceReinit) {
                    element[0].noUiSlider.destroy()
                    element[0].noUiSlider = null
                } else {
                    return
                }
            }

            const minYear = parseInt(element.data('min-year'))
            const maxYear = parseInt(element.data('max-year'))
            const currentFromYear = parseInt(element.data('current-range-from'))
            const currentToYear = parseInt(element.data('current-range-to'))

            if (!minYear || !maxYear || maxYear <= minYear) {
                console.warn('Invalid year range for year slider')
                return
            }

            if (element.length > 0) {
                const $form = $('#cars-filter-form')
                const $wrapper = element.closest('.year-slider-wrapper')

                let yearFormat = wNumb({
                    decimals: 0,
                    thousand: '',
                    prefix: '',
                })

                noUiSlider.create(element[0], {
                    start: [currentFromYear || minYear, currentToYear || maxYear],
                    tooltips: [wNumb({ decimals: 0 }), wNumb({ decimals: 0 })],
                    step: 1,
                    range: {
                        min: minYear,
                        max: maxYear,
                    },
                    format: yearFormat,
                    connect: true,
                })

                element[0].noUiSlider.on('update', function (values, handle) {
                    $wrapper.find('.year-from').text(values[0])
                    $wrapper.find('.year-to').text(values[1])
                    $form.find('input[name="year_from"]').val(yearFormat.from(values[0]))
                    $form.find('input[name="year_to"]').val(yearFormat.from(values[1]))
                })

                let yearTimeout
                element[0].noUiSlider.on('change', function (values, handle) {
                    clearTimeout(yearTimeout)
                    yearTimeout = setTimeout(function () {
                        const $form = $('#cars-filter-form')
                        if ($form.length) {
                            $form.trigger('submit')
                        }
                    }, 500)
                })
            }
        }

        const initMileageSlider = (element, forceReinit = false) => {
            if (!element || element.length === 0) return

            if (element[0] && element[0].noUiSlider) {
                if (forceReinit) {
                    element[0].noUiSlider.destroy()
                    element[0].noUiSlider = null
                } else {
                    return
                }
            }

            const maxMileage = parseInt(element.data('max-mileage-range'))
            const currentMileage = parseInt(element.data('current-range')) || maxMileage

            if (!maxMileage || maxMileage <= 0) {
                console.warn('Invalid max mileage for mileage slider')
                return
            }

            if (element.length > 0) {
                const $form = $('#cars-filter-form')
                const $wrapper = element.closest('.mileage-slider-wrapper')

                let mileageFormat = wNumb({
                    decimals: 0,
                    thousand: ',',
                    prefix: '',
                })

                noUiSlider.create(element[0], {
                    start: currentMileage,
                    tooltips: [wNumb({ decimals: 0 })],
                    step: 1,
                    range: {
                        min: 0,
                        max: maxMileage,
                    },
                    format: mileageFormat,
                    connect: 'lower',
                })

                element[0].noUiSlider.on('update', function (values, handle) {
                    $form.find('input[name="mileage_from"]').val(0)

                    const mileageValue = values[0] || '0'
                    const numericValue =
                        typeof mileageValue === 'string' ? mileageValue.replace(/,/g, '') : mileageValue
                    $form.find('input[name="mileage_to"]').val(numericValue)

                    $wrapper.find('.mileage-from').text('0 miles')
                    $wrapper.find('.mileage-to').text(parseInt(numericValue).toLocaleString() + ' miles')
                })

                let mileageTimeout
                element[0].noUiSlider.on('change', function (values, handle) {
                    clearTimeout(mileageTimeout)
                    mileageTimeout = setTimeout(function () {
                        const $form = $('#cars-filter-form')
                        if ($form.length) {
                            $form.trigger('submit')
                        }
                    }, 500)
                })
            }
        }

        const reinitializeFilterWidgets = ($context = $(document), options = {}) => {
            const forceReinit = options.force === true
            const $scope = $context && $context.length ? $context : $(document)

            const findWithinScope = (selector) => {
                if ($scope[0] === document) {
                    return $(selector)
                }

                return $scope.find(selector)
            }

            const sliderConfigs = [
                { selector: '#slider-range', handler: initUiSlider },
                { selector: '#horsepower-slider-range', handler: initHorsepowerSlider },
                { selector: '#year-slider-range', handler: initYearSlider },
                { selector: '#mileage-slider-range', handler: initMileageSlider },
            ]

            sliderConfigs.forEach(({ selector, handler }) => {
                findWithinScope(selector).each(function () {
                    handler($(this), forceReinit)
                })
            })

            const $locationSelect = findWithinScope('.select-location')

            if ($locationSelect.length > 0) {
                initSelect2($locationSelect)
            }

            initLocationAutocomplete($scope)
        }

        const $desktopFilterSection = $('.filter-section--desktop')
        const $mobileFilterSection = $('.filter-section--mobile')

        if ($desktopFilterSection.length > 0) {
            reinitializeFilterWidgets($desktopFilterSection)
        } else {
            reinitializeFilterWidgets($(document))
        }

        if ($mobileFilterSection.length > 0) {
            reinitializeFilterWidgets($mobileFilterSection)
        }

        initFilterImprovements()

        function initFilterImprovements() {
            $(document).on('click', '.filter-toggle-btn', function (e) {
                e.preventDefault()

                const $btn = $(this)
                const $target = $btn.closest('.filter-options-list').find($btn.data('target'))
                const $showMore = $btn.find('.show-more-text')
                const $showLess = $btn.find('.show-less-text')

                if ($target.is(':visible')) {
                    $target.slideUp(300)
                    $showMore.removeClass('d-none')
                    $showLess.addClass('d-none')
                } else {
                    $target.slideDown(300)
                    $showMore.addClass('d-none')
                    $showLess.removeClass('d-none')
                }
            })
        }

        $(() => {
            window.carListMaps = {}

            let CarListApp = {}
            window.__carListApp = CarListApp

            CarListApp.$formSearch = $('#cars-filter-form')
            CarListApp.carListing = '.cars-listing'
            CarListApp.$carListing = $(CarListApp.carListing)
            CarListApp.parseParamsSearch = function (query, includeArray = false) {
                let pairs = query || window.location.search.substring(1)
                let re = /([^&=]+)=?([^&]*)/g
                let decodeRE = /\+/g
                let decode = function (str) {
                    return decodeURIComponent(str.replace(decodeRE, ' '))
                }

                let params = {},
                    e
                while ((e = re.exec(pairs))) {
                    let k = decode(e[1]),
                        v = decode(e[2])
                    if (k.substring(k.length - 2) === '[]') {
                        if (includeArray) {
                            k = k.substring(0, k.length - 2)
                        }
                        ;(params[k] || (params[k] = [])).push(v)
                    } else params[k] = v
                }
                return params
            }

            CarListApp.changeInputInSearchForm = function (parseParams) {
                const updateFormInputs = ($form) => {
                    if (!$form || !$form.length) {
                        return
                    }

                    $form.find('input, select, textarea').each(function (e, i) {
                        CarListApp.changeInputInSearchFormDetail($(i), parseParams)
                    })

                    const formId = $form.attr('id')
                    if (formId) {
                        $(`:input[form=${formId}]`).each(function (e, i) {
                            CarListApp.changeInputInSearchFormDetail($(i), parseParams)
                        })
                    }
                }

                updateFormInputs(CarListApp.$formSearch)
            }

            CarListApp.changeInputInSearchFormDetail = function ($el, parseParams) {
                const name = $el.attr('name')
                const normalizedName = name && name.endsWith('[]') ? name.slice(0, -2) : name
                let value = parseParams[normalizedName]

                if (typeof value === 'undefined') {
                    value = parseParams[name]
                }
                const type = $el.attr('type')
                switch (type) {
                    case 'checkbox':
                    case 'radio':
                        $el.prop('checked', false)
                        if (Array.isArray(value)) {
                            $el.prop('checked', value.includes($el.val()))
                        } else {
                            $el.prop('checked', !!value)
                        }
                        break
                    default:
                        if ($el.is('[name=max_price]')) {
                            $el.val(value || $el.data('max'))
                        } else if ($el.is('[name=min_price]')) {
                            $el.val(value || $el.data('min'))
                        } else if ($el.val() !== value) {
                            $el.val(value)
                        }
                        break
                }
            }

            CarListApp.convertFromDataToArray = function (formData) {
                let data = []
                const grouped = {}

                formData.forEach(function (obj) {
                    if (
                        obj.name === 'rental_rate_to' &&
                        parseInt(obj.value) === parseInt($('input[name="rental_rate_to"]').data('default-value'))
                    ) {
                        return
                    }

                    if (
                        obj.name === 'horsepower_to' &&
                        parseInt(obj.value) === parseInt($('input[name="horsepower_to"]').data('default-value'))
                    ) {
                        return
                    }

                    if (obj.name === '_token') return

                    if (!obj.value) {
                        return
                    }

                    if (['min_price', 'max_price'].includes(obj.name)) {
                        const dataValue = CarListApp.$formSearch
                            .find('input[name=' + obj.name + ']')
                            .data(obj.name.substring(0, 3))
                        if (dataValue === parseInt(obj.value)) {
                            return
                        }
                    }

                    if (!grouped[obj.name]) {
                        grouped[obj.name] = []
                    }

                    grouped[obj.name].push(obj.value)
                })

                Object.entries(grouped).forEach(([name, values]) => {
                    if (name.endsWith('[]')) {
                        values.forEach((value) => {
                            data.push({ name, value })
                        })
                    } else {
                        data.push({ name, value: values[values.length - 1] })
                    }
                })

                return data
            }

            CarListApp.carsFilter = function () {
                let ajaxSending = null
                let isSubmitting = false

                $(document).on('submit', '#cars-filter-form', function (e) {
                    e.preventDefault()

                    // Prevent concurrent submissions (Safari iOS fix)
                    if (isSubmitting) {
                        return false
                    }

                    if ($(document).find('.sidebar-filter-mobile').hasClass('active')) {
                        $(document).find('.sidebar-filter-mobile').removeClass('active')

                        $('html, body').animate({
                            scrollTop: $('.car-content-section').offset().top - 150,
                        })
                    }

                    if (ajaxSending) {
                        ajaxSending.abort()
                    }

                    isSubmitting = true

                    const $form = $(e.currentTarget)
                    let formData = $form.serializeArray()
                    let data = CarListApp.convertFromDataToArray(formData)
                    const searchParams = new URLSearchParams()
                    let location = window.location
                    let nextHref = location.origin + location.pathname

                    $.urlParam = function (name) {
                        let results = new RegExp('[?&]' + name + '=([^&#]*)').exec(window.location.search)

                        return results !== null ? results[1] || 0 : false
                    }
                    if ($.urlParam('limit')) {
                        data.push({ name: 'limit', value: parseInt($.urlParam('limit')) })
                    }

                    const $elPage = CarListApp.$carListing.find('input[name=page]')
                    if ($elPage.val()) {
                        data.push({ name: 'page', value: $elPage.val() })
                    }

                    data.forEach(function (obj) {
                        if (obj.name === 'rental_rate_to') {
                            try {
                                obj.value =
                                    obj.value && typeof obj.value === 'string'
                                        ? Number(obj.value.replace(/[^0-9.-]+/g, ''))
                                        : Number(obj.value) || 0
                            } catch (e) {
                                obj.value = 0
                            }
                        }

                        if (obj.name === 'horsepower_to') {
                            try {
                                obj.value =
                                    obj.value && typeof obj.value === 'string'
                                        ? Number(obj.value.replace(/[^0-9.-]+/g, ''))
                                        : Number(obj.value) || 0
                            } catch (e) {
                                obj.value = 0
                            }
                        }

                        if (
                            obj.name === 'rental_rate_to' &&
                            parseInt($('input[name="rental_rate_to"]').data('default-value')) === parseInt(obj.value)
                        ) {
                            return
                        }

                        if (
                            obj.name === 'horsepower_to' &&
                            parseInt($('input[name="horsepower_to"]').data('default-value')) === parseInt(obj.value)
                        ) {
                            return
                        }

                        if (obj.name === 'rental_rate_from' && !parseInt(obj.value)) {
                            return
                        }

                        if (obj.name === 'rental_rate_to' && !parseInt(obj.value)) {
                            return
                        }

                        if (obj.name === 'horsepower_from' && !parseInt(obj.value)) {
                            return
                        }

                        if (obj.name === 'horsepower_to' && !parseInt(obj.value)) {
                            return
                        }

                        if (obj.name === 'page' && parseInt(obj.value) === 1) {
                            return
                        }

                        searchParams.append(obj.name, obj.value)
                    })

                    if ([...searchParams.keys()].length) {
                        nextHref += `?${searchParams.toString()}`
                    }

                    ajaxSending = $.ajax({
                        url: $form.attr('action'),
                        type: 'GET',
                        data,
                        beforeSend: function () {
                            $('#loading').css('display', 'flex')
                            $('.car-items').css('opacity', 0.2)
                        },
                        success: function ({ error, data, additional, message }) {
                            if (error) {
                                Theme.showError(message || 'Opp!')

                                return
                            }

                            CarListApp.$carListing.html($(data).html())

                            const $filterSection = $form.closest('.filter-section')

                            if ($filterSection.length && additional && additional.filters_html) {
                                $filterSection.html($(additional.filters_html).html())
                                reinitializeFilterWidgets($filterSection)
                                CarListApp.$formSearch = $filterSection.find('#cars-filter-form')
                            } else {
                                CarListApp.$formSearch = $('#cars-filter-form')
                            }

                            const parsedParams = CarListApp.parseParamsSearch(searchParams.toString(), true)
                            CarListApp.changeInputInSearchForm(parsedParams)

                            if (additional && additional.message) {
                                CarListApp.$carListing
                                    .closest('.cars-listing-container')
                                    .find('.showing-of-results')
                                    .html(additional.message)
                            }

                            if (nextHref !== window.location.href) {
                                window.history.pushState(data, message, nextHref)
                            }
                        },
                        error: function (error) {
                            if (error.statusText === 'abort') {
                                isSubmitting = false
                                return
                            }
                            handleError(error)
                        },
                        complete: function () {
                            isSubmitting = false
                            updateUrlResetFilter()
                            setTimeout(function () {
                                $('#loading').css('display', 'none')
                                $('.loading-ajax').hide()
                                $('.car-items').css('opacity', 1)
                            }, 500)
                        },
                    })
                })

                window.addEventListener(
                    'popstate',
                    function () {
                        window.location.reload()
                    },
                    false
                )

                $(document).on('click', '.cars-listing .pagination a', function (e) {
                    e.preventDefault()
                    let aLink = $(e.currentTarget).attr('href')

                    if (!aLink.includes(window.location.protocol)) {
                        aLink = window.location.protocol + aLink
                    }

                    let url = new URL(aLink)
                    let page = url.searchParams.get('page')
                    CarListApp.$formSearch.find('input[name=page]').val(page)
                    CarListApp.$formSearch.trigger('submit')
                })
            }

            CarListApp.carsFilter()

            const copyFormValues = ($sourceForm, $targetForm) => {
                if (!$sourceForm.length || !$targetForm.length) {
                    return
                }

                const targetFormId = $targetForm.attr('id')

                const resetFields = ($form) => {
                    $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false)
                }

                resetFields($targetForm)

                const applyValue = (name, value) => {
                    const applyToCollection = ($collection) => {
                        if (!$collection.length) {
                            return false
                        }

                        const type = ($collection.first().attr('type') || '').toLowerCase()

                        if (type === 'checkbox' || type === 'radio') {
                            $collection
                                .filter(function () {
                                    return $(this).val() == value
                                })
                                .prop('checked', true)
                        } else {
                            $collection.val(value)
                        }

                        return true
                    }

                    if (applyToCollection($targetForm.find(`[name="${name}"]`))) {
                        return
                    }

                    if (targetFormId) {
                        applyToCollection($(`:input[form="${targetFormId}"][name="${name}"]`))
                    }
                }

                const formData = $sourceForm.serializeArray()
                formData.forEach(({ name, value }) => applyValue(name, value))
            }

            const resetFilterForm = ($form) => {
                if (!$form.length) {
                    return
                }

                $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false)
                $form.find('input[type="text"], select').val('')
                $form.find('input[name="page"]').val(1)

                $form.find('input[type="hidden"]').each(function () {
                    const name = $(this).attr('name')

                    if (['_token', 'layout', 'layout_col', 'filter', 'sort_by', 'per_page'].includes(name)) {
                        return
                    }

                    $(this).val('')
                })
            }

            const syncDesktopToMobile = () => {
                copyFormValues($('#cars-filter-form'), $('#cars-filter-form-mobile'))
            }

            const syncMobileToDesktop = () => {
                copyFormValues($('#cars-filter-form-mobile'), $('#cars-filter-form'))
            }

            $(document).on('change', '.submit-form-filter', function (e) {
                if (e.target.name === 'location') {
                    $('input[name=location]').val(e.target.value)
                }

                const $target = $(e.currentTarget)
                CarListApp.$formSearch.find('input[name="page"]').val(1)
                submitForm(e)
            })

            $(document).on('click', '.btn-clear-mobile-filters', function (e) {
                e.preventDefault()

                const $mobileForm = $('#cars-filter-form-mobile')
                resetFilterForm($mobileForm)
                resetFilterForm($('#cars-filter-form'))
                syncMobileToDesktop()

                const mobileOffcanvas = document.getElementById('mobileFiltersOffcanvas')
                if (mobileOffcanvas) {
                    const instance = bootstrap.Offcanvas.getInstance(mobileOffcanvas)
                    instance?.hide()
                }

                $('#cars-filter-form').trigger('submit')
            })

            $(document).on('click', '.btn-apply-mobile-filters', function () {
                syncMobileToDesktop()

                const mobileOffcanvas = document.getElementById('mobileFiltersOffcanvas')
                if (mobileOffcanvas) {
                    const instance = bootstrap.Offcanvas.getInstance(mobileOffcanvas)
                    instance?.hide()
                }

                $('#cars-filter-form').trigger('submit')
            })

            $(document).on('show.bs.offcanvas', '#mobileFiltersOffcanvas', function () {
                syncDesktopToMobile()
            })

            String.prototype.interpolate = function (params) {
                const names = Object.keys(params)
                const vals = Object.values(params)
                return new Function(...names, `return \`${this}\`;`)(...vals)
            }

            if ($('.cars-list-sidebar').length) {
                if ($('.cars-list-sidebar').is(':visible')) {
                    CarListApp.initMaps($('.cars-list-sidebar').find('.cars-list-map'))
                }

                $(window).on('resize', function () {
                    if ($('.cars-list-sidebar').is(':visible')) {
                        CarListApp.initMaps($('.cars-list-sidebar').find('.cars-list-map'))
                    }
                })
            }

            CarListApp.setCookie = function (cname, cvalue, exdays) {
                let d = new Date()
                let siteUrl = window.siteUrl

                if (!siteUrl.includes(window.location.protocol)) {
                    siteUrl = window.location.protocol + siteUrl
                }

                let url = new URL(siteUrl)
                d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000)
                let expires = 'expires=' + d.toUTCString()
                document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/' + '; domain=' + url.hostname
            }
        })

        $(document).on('click', '.layout-car', function (e) {
            e.preventDefault()

            $('#cars-filter-form > input[name=layout]').val($(this).data('layout'))
            $('#cars-filter-form').submit()
        })

        $(document).on('click', '.dropdown-sort-by', function (e) {
            e.preventDefault()

            $('#cars-filter-form input[name=sort_by]').val($(this).data('sortBy'))
            $('#cars-filter-form').submit()
        })

        $(document).on('click', '.per-page-item', function (e) {
            e.preventDefault()

            $('#cars-filter-form input[name=per_page]').val($(this).data('perPage'))
            $('#cars-filter-form').submit()
        })

        $(document).on('change', '.box-filters-sidebar.vehicle-condition select', function (e) {
            e.preventDefault()

            $('#cars-filter-form').submit()
        })

        $(document).on('click', '.car-filter-checkbox .link-see-more-js', function (e) {
            e.preventDefault()

            $(this).hide()
            $(this).parent().parent().find('.list-filter-checkbox li').css('display', 'flex')
            $(this).parent().find('.link-see-less').css('display', 'flex')
        })

        $(document).on('click', '.car-filter-checkbox .link-see-less', function (e) {
            e.preventDefault()

            $(this).hide()
            $(this).parent().parent().find('.list-filter-checkbox li:nth-child(n+6)').css('display', 'none')
            $(this).parent().find('.link-see-more-js').css('display', 'flex')
        })

        function updateUrlResetFilter() {
            const $btnResetFilter = $('.link-reset')

            if ($btnResetFilter.length) {
                $btnResetFilter.attr('href', window.location.href.split('?')[0])
            }
        }

        if (!$('.sidebar-filter-mobile').length && $('.btn-advanced-filter').length) {
            $('.btn-advanced-filter').hide()
        }

        updateUrlResetFilter()
    })

    $('.shortcode-cars .popular-categories ul li').on('click', function (event) {
        event.target.parentElement.parentElement.parentElement.querySelector('input').value =
            event.target.getAttribute('data-value')
        const ajaxUrl = $('#popular-vehicle-url').val()
        const limitItems = $('#popular-vehicle-limit').val()
        const category = $('#filter-popular-category input').val()
        const fuel = $('#filter-popular-fuel input').val()
        const order = $('#filter-popular-order input').val()
        const priceRange = $('#filter-popular-price input').val()
        const param = {
            limit: limitItems,
            category: category,
            fuel_type: fuel,
            order: order,
            price_range: priceRange,
        }

        ajaxSearchPopularVehicles(ajaxUrl, '#content-popular-vehicles', param)
    })

    function ajaxSearchPopularVehicles(url, elApply, param) {
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            data: param,
            beforeSend: function () {},
            success: (response) => {
                if (!response.error) {
                    $(elApply).html(response.data)
                    if (response.additional.total) {
                        $('#popular-vehicle-load-more').attr('style', '')
                    } else {
                        $('#popular-vehicle-load-more').attr('style', 'display:none !important')
                    }
                } else {
                    $(elApply).html('<p class="text-xl-medium neutral-500">No matching vehicle information found</p>')
                    $('#popular-vehicle-load-more').attr('style', 'display:none !important')
                }

                new Swiper(elApply + ' .swiper-group-2', {
                    slidesPerView: 2,
                    spaceBetween: 30,
                    slidesPerGroup: 1,
                    loop: true,
                    navigation: {
                        nextEl: '.swiper-button-next-2',
                        prevEl: '.swiper-button-prev-2',
                    },
                    autoplay: {
                        delay: 10000,
                    },
                    breakpoints: {
                        1199: {
                            slidesPerView: 2,
                        },
                        800: {
                            slidesPerView: 1,
                        },
                        400: {
                            slidesPerView: 1,
                        },
                        250: {
                            slidesPerView: 1,
                        },
                    },
                })
            },
            error: () => {},
            complete: () => {},
        })
    }

    $('.filter-brands-by-alphabet').on('click', '[data-bb-toggle="filter-brands"]', function () {
        const currentTarget = $(this)

        let currentValue = currentTarget.attr('data-bb-value')

        if (currentTarget.hasClass('active')) {
            currentTarget.removeClass('active')

            currentValue = null
        } else {
            $('[data-bb-toggle="filter-brands"]').each(function () {
                $(this).removeClass('active')
            })

            currentTarget.addClass('active')
        }

        $('[data-bb-toggle="brand-item"]').each(function () {
            let currentElement = $(this)

            let currentElementValue = currentElement.attr('data-bb-value')

            if (!currentValue) {
                currentElement.show()
                return
            }

            if (currentElementValue === currentValue) {
                currentElement.show()
            } else {
                currentElement.hide()
            }
        })
    })

    $(document).on('submit', '.bb-car-rentals-message-form', function (event) {
        event.preventDefault()
        event.stopPropagation()

        const $form = $(this)
        const $button = $form.find('button[type=submit]')

        $.ajax({
            type: 'POST',
            cache: false,
            url: $form.prop('action'),
            data: new FormData($form[0]),
            contentType: false,
            processData: false,
            beforeSend: () => $button.addClass('btn-loading'),
            success: ({ error, message }) => {
                if (!error) {
                    $form[0].reset()
                    Theme.showSuccess(message)
                } else {
                    Theme.showError(message)
                }
            },
            error: (error) => {
                Theme.handleError(error)
            },
            complete: () => {
                if (typeof refreshRecaptcha !== 'undefined') {
                    refreshRecaptcha()
                }

                $button.removeClass('btn-loading')
            },
        })
    })

    if ($('#upgrade-form').length > 0) {
        var $agreeTerms = $('#agree-terms')
        var $upgradeButton = $('#upgrade-button')

        function updateButtonState() {
            $upgradeButton.prop('disabled', !$agreeTerms.is(':checked'))
        }

        updateButtonState()

        $agreeTerms.on('change', function () {
            updateButtonState()
        })

        $('#upgrade-form').on('submit', function (e) {
            e.preventDefault()

            var $form = $(this)
            var originalButtonText = $upgradeButton.html()

            $upgradeButton
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>Processing...')

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function (response) {
                    if (response.error) {
                        if (typeof Botble !== 'undefined') {
                            Botble.showError(response.message)
                        } else {
                            alert(response.message)
                        }
                        $upgradeButton.prop('disabled', false).html(originalButtonText)
                        updateButtonState()
                    } else {
                        if (typeof Botble !== 'undefined') {
                            Botble.showSuccess(response.message)
                        } else {
                            alert(response.message)
                        }
                        if (response.data && response.data.next_url) {
                            setTimeout(function () {
                                window.location.href = response.data.next_url
                            }, 1500)
                        } else {
                            setTimeout(function () {
                                window.location.reload()
                            }, 1500)
                        }
                    }
                },
                error: function (xhr) {
                    var response = xhr.responseJSON
                    var errorMessage =
                        response && response.message ? response.message : 'An error occurred. Please try again.'
                    if (typeof Botble !== 'undefined') {
                        Botble.showError(errorMessage)
                    } else {
                        alert(errorMessage)
                    }
                    $upgradeButton.prop('disabled', false).html(originalButtonText)
                    updateButtonState()
                },
            })
        })
    }
})(jQuery)
