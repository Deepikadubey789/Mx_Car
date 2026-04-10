class CarPricingCalendar {
    constructor(element) {
        this.container = element
        this.url = element.dataset.url
        this.calendar = null
        this.modal = null
        this.form = {
            id: '',
            value: '',
            value_type: 'fixed',
            start_date: '',
            end_date: '',
            active: 1,
        }

        this.init()
    }

    init() {
        this.initCalendar()
        this.initForm()
    }

    initCalendar() {
        const calendarEl = this.container.querySelector('#pricing-calendar')
        if (!calendarEl) return

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
            events: { url: this.url },
            loading: (isLoading) => {
                calendarEl.classList.toggle('loading', isLoading)
            },
            select: (arg) => {
                const endDate = moment(arg.end).subtract(1, 'days').format('YYYY-MM-DD')
                this.showModal({
                    start_date: moment(arg.start).format('YYYY-MM-DD'),
                    end_date: endDate,
                    value: '',
                    value_type: 'fixed',
                    active: 1,
                })
            },
            eventClick: (info) => {
                const form = { ...info.event.extendedProps }
                form.start_date = moment(info.event.start).format('YYYY-MM-DD')
                form.end_date = moment(info.event.start).format('YYYY-MM-DD')
                this.showModal(form)
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
    }

    initForm() {
        const modal = this.resolveModal()
        if (!modal) return

        const form = modal.querySelector('form')
        const activeCheckbox = modal.querySelector('#pricing-active')
        const conditionalFields = modal.querySelector('#conditional-fields')
        const valueTypeSelect = modal.querySelector('#pricing-value-type')

        // Toggle conditional fields visibility
        activeCheckbox?.addEventListener('change', () => {
            conditionalFields.style.display = activeCheckbox.checked ? 'block' : 'none'
        })

        // Toggle info alerts based on value type
        valueTypeSelect?.addEventListener('change', () => {
            this.updateInfoAlerts(valueTypeSelect.value)
        })

        // Handle form submission
        form?.addEventListener('submit', (e) => {
            e.preventDefault()
            this.saveForm()
        })

        // Handle save button click
        modal.querySelector('.btn-save')?.addEventListener('click', () => {
            this.saveForm()
        })
    }

    resolveModal() {
        if (this.modal) {
            return this.modal
        }

        const modal = this.container.querySelector('#modal-pricing-calendar')
        if (!modal) {
            return null
        }

        // Keep modal at document root so bootstrap backdrop stacking is correct.
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal)
        }

        this.modal = modal

        modal.addEventListener('hidden.bs.modal', () => {
            this.normalizeBackdrops()
        })

        return this.modal
    }

    normalizeBackdrops() {
        const backdrops = document.querySelectorAll('.modal-backdrop')

        if (backdrops.length > 1) {
            backdrops.forEach((backdrop, index) => {
                if (index < backdrops.length - 1) {
                    backdrop.remove()
                }
            })
        }

        if (!document.querySelector('.modal.show')) {
            document.body.classList.remove('modal-open')
            document.body.style.removeProperty('padding-right')
        }
    }

    updateInfoAlerts(valueType) {
        const modal = this.resolveModal()
        if (!modal) return

        const percentageInfo = modal.querySelector('#pricing-percentage-info')
        const amountInfo = modal.querySelector('#pricing-amount-info')

        if (percentageInfo) {
            percentageInfo.style.display = valueType === 'percentage_adjust' ? 'block' : 'none'
        }
        if (amountInfo) {
            amountInfo.style.display = valueType === 'amount_adjust' ? 'block' : 'none'
        }
    }

    showModal(data) {
        this.form = { ...data }
        const modal = this.resolveModal()
        if (!modal) return

        // Update modal title
        const title = data.start_date === data.end_date
            ? moment(data.start_date).format('MMM DD, YYYY')
            : moment(data.start_date).format('MMM DD') + ' - ' + moment(data.end_date).format('MMM DD, YYYY')
        modal.querySelector('.modal-title').textContent = title

        // Fill form fields
        const activeCheckbox = modal.querySelector('#pricing-active')
        activeCheckbox.checked = data.active == 1
        modal.querySelector('#pricing-value').value = data.value || ''
        modal.querySelector('#pricing-value-type').value = data.value_type || 'fixed'

        // Show/hide conditional fields
        const conditionalFields = modal.querySelector('#conditional-fields')
        conditionalFields.style.display = activeCheckbox.checked ? 'block' : 'none'

        // Update info alerts
        this.updateInfoAlerts(data.value_type || 'fixed')

        // Show modal
        $(modal).modal('show')
    }

    hideModal() {
        const modal = this.resolveModal()
        if (!modal) return

        $(modal).modal('hide')
    }

    saveForm() {
        const modal = this.resolveModal()
        if (!modal) return

        const saveBtn = modal.querySelector('.btn-save')

        // Get form data
        const data = {
            start_date: this.form.start_date,
            end_date: this.form.end_date,
            active: modal.querySelector('#pricing-active').checked ? 1 : 0,
            value: modal.querySelector('#pricing-value').value,
            value_type: modal.querySelector('#pricing-value-type').value,
        }

        if (this.form.id) {
            data.id = this.form.id
        }

        saveBtn.classList.add('button-loading')

        $.ajax({
            url: this.url,
            data: data,
            dataType: 'json',
            method: 'POST',
            success: (res) => {
                if (!res.error) {
                    this.calendar?.refetchEvents()
                    this.hideModal()
                    Botble.showSuccess(res.message)
                } else {
                    Botble.showError(res.message)
                }
                saveBtn.classList.remove('button-loading')
            },
            error: (xhr) => {
                saveBtn.classList.remove('button-loading')
                if (xhr.responseJSON?.message) {
                    Botble.showError(xhr.responseJSON.message)
                }
            },
        })
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.car-pricing-calendar-wrapper').forEach(el => {
        new CarPricingCalendar(el)
    })
})
