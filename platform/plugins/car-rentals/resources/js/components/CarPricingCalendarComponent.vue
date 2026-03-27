<template>
    <div>
        <div id="pricing-calendar" class="pricing-calendar mb-3"></div>

        <div class="d-flex flex-wrap gap-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-azure text-azure-fg">$0</span>
                <small class="text-muted">{{ trans.base_price }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-blue text-blue-fg">$0</span>
                <small class="text-muted">{{ trans.fixed }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-green text-green-fg">$0</span>
                <small class="text-muted">{{ trans.amount_adjust }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-orange text-orange-fg">$0</span>
                <small class="text-muted">{{ trans.percentage_adjust }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">$0</span>
                <small class="text-muted">{{ trans.inactive }}</small>
            </div>
        </div>

        <div class="modal fade" id="modal-pricing-calendar" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ trans.set_pricing }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="pricing-active"
                                    v-model="form.active"
                                    true-value="1"
                                    false-value="0"
                                >
                                <label class="form-check-label" for="pricing-active">
                                    {{ trans.is_active }}
                                </label>
                            </div>
                        </div>

                        <div v-show="form.active == 1">
                            <div class="mb-3">
                                <label class="form-label">{{ trans.value }}</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    step="0.01"
                                    v-model="form.value"
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ trans.value_type }}</label>
                                <select class="form-select" v-model="form.value_type">
                                    <option value="fixed">{{ trans.fixed }}</option>
                                    <option value="amount_adjust">{{ trans.amount_adjust }}</option>
                                    <option value="percentage_adjust">{{ trans.percentage_adjust }}</option>
                                </select>
                            </div>

                            <div class="alert alert-info" v-if="form.value_type === 'percentage_adjust'">
                                {{ trans.percentage_info }}
                            </div>
                            <div class="alert alert-info" v-if="form.value_type === 'amount_adjust'">
                                {{ trans.amount_info }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ trans.cancel }}
                        </button>
                        <button type="button" class="btn btn-primary" @click="saveForm" :disabled="onSubmit">
                            {{ trans.save }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        getPricingUrl: {
            type: String,
            required: true,
        },
        translations: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            form: {
                id: '',
                value: '',
                value_type: 'fixed',
                start_date: '',
                end_date: '',
                active: 1,
            },
            formDefault: {
                id: '',
                value: '',
                value_type: 'fixed',
                start_date: '',
                end_date: '',
                active: 1,
            },
            onSubmit: false,
            calendar: null,
        }
    },
    computed: {
        trans() {
            return {
                base_price: this.translations.base_price || 'Base Price',
                fixed: this.translations.fixed || 'Fixed Price',
                amount_adjust: this.translations.amount_adjust || 'Amount Adjustment',
                percentage_adjust: this.translations.percentage_adjust || 'Percentage Adjustment',
                inactive: this.translations.inactive || 'Inactive',
                set_pricing: this.translations.set_pricing || 'Set Pricing',
                is_active: this.translations.is_active || 'Enable custom pricing',
                value_type: this.translations.value_type || 'Pricing Type',
                value: this.translations.value || 'Price Value',
                percentage_info: this.translations.percentage_info || 'Use positive for markup, negative for discount',
                amount_info: this.translations.amount_info || 'Amount to add or subtract from base price',
                cancel: this.translations.cancel || 'Cancel',
                save: this.translations.save || 'Save',
            }
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.initCalendar()
        })
    },
    methods: {
        initCalendar() {
            const calendarEl = document.getElementById('pricing-calendar')
            if (!calendarEl) {
                return
            }

            if (this.calendar) {
                this.calendar.destroy()
            }

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                locale: $('html').prop('lang') || 'en',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: '',
                },
                initialView: 'dayGridMonth',
                selectable: true,
                selectMirror: true,
                navLinks: true,
                editable: false,
                dayMaxEvents: 1,
                events: {
                    url: this.getPricingUrl,
                },
                loading: (isLoading) => {
                    if (isLoading) {
                        $(calendarEl).addClass('loading')
                    } else {
                        $(calendarEl).removeClass('loading')
                    }
                },
                select: (arg) => {
                    const endDate = moment(arg.end).subtract(1, 'days').format('YYYY-MM-DD')
                    this.show({
                        start_date: moment(arg.start).format('YYYY-MM-DD'),
                        end_date: endDate,
                        value: '',
                        value_type: 'fixed',
                        active: 1,
                    })
                },
                eventClick: (info) => {
                    const form = Object.assign({}, info.event.extendedProps)
                    form.start_date = moment(info.event.start).format('YYYY-MM-DD')
                    form.end_date = moment(info.event.start).format('YYYY-MM-DD')
                    this.show(form)
                },
                eventContent: (arg) => {
                    const isInactive = arg.event.extendedProps.classNames?.includes('inactive')
                    const style = isInactive ? 'text-decoration: line-through; opacity: 0.7;' : ''
                    return {
                        html: '<div class="fc-event-title" style="' + style + '">' + arg.event.title + '</div>'
                    }
                },
            })

            this.calendar.render()
        },
        show(form) {
            $('#modal-pricing-calendar').modal('show')
            this.onSubmit = false

            if (typeof form !== 'undefined') {
                this.form = Object.assign({}, this.formDefault, form)

                if (form.start_date) {
                    const title = form.start_date === form.end_date
                        ? moment(form.start_date).format('MMM DD, YYYY')
                        : moment(form.start_date).format('MMM DD') + ' - ' + moment(form.end_date).format('MMM DD, YYYY')
                    $('#modal-pricing-calendar .modal-title').text(title)
                }
            }
        },
        hide() {
            $('#modal-pricing-calendar').modal('hide')
            this.form = Object.assign({}, this.formDefault)
        },
        saveForm() {
            if (this.onSubmit) {
                return
            }

            if (!this.validateForm()) {
                return
            }

            $('#modal-pricing-calendar').find('.btn-primary').addClass('button-loading')

            this.onSubmit = true
            $.ajax({
                url: this.getPricingUrl,
                data: this.form,
                dataType: 'json',
                method: 'POST',
                success: (res) => {
                    if (!res.error) {
                        if (this.calendar) {
                            this.calendar.refetchEvents()
                        }
                        this.hide()
                        Botble.showSuccess(res.message)
                    } else {
                        Botble.showError(res.message)
                    }
                    this.onSubmit = false
                    $('#modal-pricing-calendar').find('.btn-primary').removeClass('button-loading')
                },
                error: (xhr) => {
                    this.onSubmit = false
                    $('#modal-pricing-calendar').find('.btn-primary').removeClass('button-loading')
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        Botble.showError(xhr.responseJSON.message)
                    }
                },
            })
        },
        validateForm() {
            return !(!this.form.start_date || !this.form.end_date)
        },
    },
}
</script>
