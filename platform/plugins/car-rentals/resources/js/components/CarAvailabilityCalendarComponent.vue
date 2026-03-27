<script>
export default {
    props: {
        eventsUrl: {
            type: String,
            required: true,
        },
        availabilityUrl: {
            type: String,
            required: true,
        },
        bookingDetailsUrl: {
            type: String,
            required: true,
        },
        cars: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            calendarInstance: null,
            selectedCar: '',
            loading: true,
            showAvailabilityCheck: false,
            availabilityData: [],
            selectedDateRange: {
                start: null,
                end: null,
            },
        }
    },

    computed: {
        trans() {
            return window.trans?.availability_calendar || {}
        }
    },

    async mounted() {
        await this.$nextTick()

        if (this.calendarInstance) {
            this.calendarInstance.destroy()
        }

        if (this.$refs.calendar) {
            this.calendarInstance = new FullCalendar.Calendar(this.$refs.calendar, {
                locale: $('html').prop('lang') || 'en',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                navLinks: true,
                editable: false,
                dayMaxEvents: true,
                selectable: true,
                selectMirror: true,
                events: (info, successCallback, failureCallback) => {
                    this.loadEvents(info, successCallback, failureCallback)
                },
                loading: (isLoading) => {
                    this.loading = isLoading
                },
                eventClick: (info) => {
                    this.loadBookingDetails(info.event.extendedProps.booking_id)
                },
                select: (info) => {
                    this.handleDateSelection(info)
                },
                eventDidMount: (info) => {
                    // Add tooltip with booking details
                    $(info.el).tooltip({
                        title: this.getEventTooltip(info.event),
                        placement: 'top',
                        trigger: 'hover',
                        html: true
                    })
                }
            })

            this.calendarInstance.render()
        }
    },

    methods: {
        loadEvents(info, successCallback, failureCallback) {
            const params = {
                start: info.startStr,
                end: info.endStr,
            }

            if (this.selectedCar) {
                params.car_id = this.selectedCar
            }

            $.get(this.eventsUrl, params)
                .done(successCallback)
                .fail(failureCallback)
        },

        filterByCar() {
            if (this.calendarInstance) {
                this.calendarInstance.refetchEvents()
            }
        },

        clearCarFilter() {
            this.selectedCar = ''
            this.filterByCar()
        },

        handleDateSelection(selectionInfo) {
            this.selectedDateRange = {
                start: selectionInfo.startStr,
                end: selectionInfo.endStr,
            }
            this.checkAvailability()
        },

        async checkAvailability() {
            if (!this.selectedDateRange.start || !this.selectedDateRange.end) {
                return
            }

            this.loading = true
            this.showAvailabilityCheck = true

            try {
                const response = await $.get(this.availabilityUrl, {
                    start: this.selectedDateRange.start,
                    end: this.selectedDateRange.end,
                })

                this.availabilityData = response
            } catch (error) {
                console.error('Error checking availability:', error)
                this.$toast.error(this.trans.error_checking_availability || 'Error checking car availability')
            } finally {
                this.loading = false
            }
        },

        async loadBookingDetails(bookingId) {
            if (!bookingId) {
                console.error('No booking ID provided')
                return
            }

            try {
                // Show loading state in modal
                $('#booking-detail-content').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">${this.trans.loading || 'Loading...'}</span>
                        </div>
                        <h4 class="text-muted">${this.trans.loading_booking_details || 'Loading booking details...'}</h4>
                        <p class="text-secondary">${this.trans.please_wait_booking_info || 'Please wait while we fetch the booking information.'}</p>
                    </div>
                `)
                $('#booking-detail-modal').modal('show')

                // Fetch booking details via AJAX
                const response = await $.get(this.bookingDetailsUrl, {
                    booking_id: bookingId
                })

                if (response.success) {
                    $('#booking-detail-content').html(response.data)
                    $('#booking-detail-link').attr('href', response.edit_url)
                } else {
                    $('#booking-detail-content').html(`
                        <div class="alert alert-danger">
                            <h4>${this.trans.error || 'Error'}</h4>
                            <p>${this.trans.error_loading_booking || 'Failed to load booking details. Please try again.'}</p>
                        </div>
                    `)
                }
            } catch (error) {
                console.error('Error loading booking details:', error)
                $('#booking-detail-content').html(`
                    <div class="alert alert-danger">
                        <h4>${this.trans.error || 'Error'}</h4>
                        <p>${this.trans.error_loading_booking || 'Failed to load booking details. Please try again.'}</p>
                    </div>
                `)
            }
        },

        getEventTooltip(event) {
            const props = event.extendedProps
            return `
                <div>
                    <strong>${props.car_name}</strong><br>
                    ${this.trans.customer || 'Customer'}: ${props.customer_name}<br>
                    ${this.trans.status || 'Status'}: ${props.status}<br>
                    ${this.trans.amount || 'Amount'}: ${props.amount}
                </div>
            `
        },

        closeAvailabilityCheck() {
            this.showAvailabilityCheck = false
            this.availabilityData = []
            this.selectedDateRange = { start: null, end: null }

            // Clear calendar selection
            if (this.calendarInstance) {
                this.calendarInstance.unselect()
            }
        },

        getAvailabilityBadgeClass(isAvailable) {
            return isAvailable ? 'text-green' : 'text-orange'
        },

        getAvailabilityText(isAvailable) {
            return isAvailable ? (this.trans.available || 'Available') : (this.trans.not_available || 'Not Available')
        },
    },

    beforeDestroy() {
        if (this.calendarInstance) {
            this.calendarInstance.destroy()
        }
    },
}
</script>

<template>
    <div class="car-availability-calendar w-full">
        <!-- Main Calendar Card -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Car Filter -->
                            <div class="input-group input-group-flat">
                                <span class="input-group-text pe-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 6h8l2 4v6a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-1H9v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6l2-4z"/>
                                        <circle cx="9" cy="16" r="1"/>
                                        <circle cx="15" cy="16" r="1"/>
                                    </svg>
                                </span>
                                <select
                                    v-model="selectedCar"
                                    @change="filterByCar"
                                    class="form-select"
                                    style="min-width: 220px;"
                                >
                                    <option value="" selected>{{ trans.all_cars || 'All Cars' }}</option>
                                    <option
                                        v-for="car in cars"
                                        :key="car.id"
                                        :value="car.id"
                                    >
                                        {{ car.name }} ({{ car.make?.name || trans.unknown_make || 'Unknown Make' }})
                                    </option>
                                </select>
                            </div>

                            <!-- Clear Filter Button -->
                            <button
                                v-if="selectedCar"
                                @click="clearCarFilter"
                                class="btn btn-outline-secondary btn-icon"
                                :title="trans.clear_filter || 'Clear Filter'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6L6 18"/>
                                    <path d="M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Status Legend -->
                <div class="d-flex flex-wrap gap-2 mb-4 p-3 bg-light rounded">
                    <div class="d-flex align-items-center">
                        <span class="status-dot bg-warning me-2"></span>
                        <small class="text-muted">{{ trans.pending || 'Pending' }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="status-dot bg-info me-2"></span>
                        <small class="text-muted">{{ trans.processing || 'Processing' }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="status-dot bg-success me-2"></span>
                        <small class="text-muted">{{ trans.completed || 'Completed' }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="status-dot bg-danger me-2"></span>
                        <small class="text-muted">{{ trans.cancelled || 'Cancelled' }}</small>
                    </div>
                    <div class="ms-auto">
                        <small class="text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            {{ trans.select_dates || 'Select dates to check availability' }}
                        </small>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div ref="calendar" class="calendar-container"></div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="card-footer">
                <div class="d-flex align-items-center justify-content-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary me-3" role="status">
                        <span class="visually-hidden">{{ trans.loading || 'Loading...' }}</span>
                    </div>
                    <span class="text-muted">{{ trans.loading_calendar_data || 'Loading calendar data...' }}</span>
                </div>
            </div>
        </div>

        <!-- Availability Check Modal -->
        <div
            v-if="showAvailabilityCheck"
            class="modal modal-blur fade show d-block"
            style="background-color: rgba(0,0,0,0.5);"
        >
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                                <path d="M9 16l2 2 4-4"/>
                            </svg>
                            {{ trans.availability_check || 'Car Availability Check' }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="closeAvailabilityCheck"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <!-- Selected Period Info -->
                        <div class="alert alert-info d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                                <rect x="9" y="12" width="6" height="6" rx="1"/>
                            </svg>
                            <div>
                                <strong>{{ trans.selected_period || 'Selected Period' }}:</strong>
                                <span class="badge bg-primary ms-2">
                                    {{ selectedDateRange.start }} to {{ selectedDateRange.end }}
                                </span>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div v-if="loading" class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">{{ trans.loading || 'Loading...' }}</span>
                            </div>
                            <h4 class="text-muted">{{ trans.checking_availability || 'Checking availability...' }}</h4>
                            <p class="text-secondary">{{ trans.please_wait_availability || 'Please wait while we check car availability for your selected dates.' }}</p>
                        </div>

                        <!-- Availability Results -->
                        <div v-else-if="availabilityData.length">
                            <div class="row row-cards">
                                <div v-for="car in availabilityData" :key="car.id" class="col-md-6 col-lg-4">
                                    <div class="card">
                                        <div class="card-status-top" :class="car.is_available ? 'bg-success' : 'bg-danger'"></div>
                                        <div class="card-body">
                                            <h3 class="card-title">{{ car.name }}</h3>
                                            <div class="text-secondary mb-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <strong>{{ trans.make || 'Make' }}:</strong><br>
                                                        {{ car.make }}
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>{{ trans.vendor || 'Vendor' }}:</strong><br>
                                                        {{ car.vendor }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <span class="h4 text-primary">{{ car.rental_rate }}</span>
                                                    <small class="text-muted">{{ trans.per_day || '/day' }}</small>
                                                </div>
                                                <span
                                                    :class="getAvailabilityBadgeClass(car.is_available)"
                                                >
                                                    <svg v-if="car.is_available" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                        <polyline points="20,6 9,17 4,12"/>
                                                    </svg>
                                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                                    </svg>
                                                    {{ getAvailabilityText(car.is_available) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <a
                                                :href="car.edit_url"
                                                class="btn btn-outline-primary w-100"
                                                target="_blank"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                {{ trans.edit_car || 'Edit Car' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="empty">
                            <div class="empty-img">
                                <img src="/static/illustrations/undraw_void_3ggu.svg" height="128" alt="">
                            </div>
                            <p class="empty-title">{{ trans.no_cars_found || 'No cars found' }}</p>
                            <p class="empty-subtitle text-secondary">
                                {{ trans.no_cars_available_period || 'There are no cars available for the selected period.' }}
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="closeAvailabilityCheck"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            {{ trans.close || 'Close' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Detail Modal -->
        <div class="modal modal-blur fade" id="booking-detail-modal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                                <rect x="9" y="12" width="6" height="6" rx="1"/>
                            </svg>
                            {{ trans.booking_details || 'Booking Details' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="booking-detail-content"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            {{ trans.close || 'Close' }}
                        </button>
                        <a
                            id="booking-detail-link"
                            href="#"
                            class="btn btn-primary"
                            target="_blank"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            {{ trans.edit_booking || 'Edit Booking' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Calendar Container */
.calendar-container {
    min-height: 600px;
}

/* Status Dots */
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

/* Card Enhancements */
.card-status-top {
    height: 3px;
}

/* Badge Improvements */
.badge-lg {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.badge {
    color: white !important;
}

/* Empty State */
.empty {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-img img {
    opacity: 0.8;
}

/* Modal Enhancements */
.modal-blur .modal-content {
    backdrop-filter: blur(10px);
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .calendar-container {
        min-height: 400px;
    }

    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }

    .input-group {
        width: 100%;
    }
}

/* Calendar Event Styling */
:deep(.fc-event) {
    border-radius: 4px;
    border: none;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
}

/* Calendar Navigation Improvements */
:deep(.fc-toolbar) {
    margin-bottom: 1rem;
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

:deep(.fc-toolbar-title) {
    font-size: 1.25rem;
    font-weight: 600;
    color: #495057;
}

/* View buttons (month, week, list) */
:deep(.fc-button-group) {
    display: inline-flex;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

:deep(.fc-button-group .fc-button) {
    background-color: #ffffff;
    border: 1px solid #dadfe5;
    color: #495057;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    margin: 0;
    border-radius: 0;
    transition: all 0.15s ease-in-out;
}

:deep(.fc-button-group .fc-button:first-child) {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

:deep(.fc-button-group .fc-button:last-child) {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

:deep(.fc-button-group .fc-button:not(:first-child)) {
    border-left: 0;
}

:deep(.fc-button-group .fc-button-active) {
    background-color: #206bc4 !important;
    border-color: #206bc4 !important;
    color: white !important;
    z-index: 1;
}

/* Navigation buttons (prev/next) */
:deep(.fc-prev-button),
:deep(.fc-next-button) {
    background-color: #ffffff !important;
    border: 1px solid #dadfe5 !important;
    color: #495057 !important;
    border-radius: 6px !important;
    padding: 0.5rem 0.75rem !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    transition: all 0.15s ease-in-out !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

/* Today button */
:deep(.fc-today-button) {
    background-color: #ffffff !important;
    border: 1px solid #dadfe5 !important;
    color: #495057 !important;
    border-radius: 6px !important;
    padding: 0.5rem 1rem !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    transition: all 0.15s ease-in-out !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

:deep(.fc-day-today) {
    background-color: rgba(var(--bb-primary-rgb), 0.1) !important;
}

:deep(.fc-daygrid-day-number) {
    color: var(--bb-body-color);
    font-weight: 500;
}

:deep(.fc-day-today .fc-daygrid-day-number) {
    background-color: var(--bb-primary);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 2px;
}

/* Loading Animation */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Improved spacing */
.gap-2 > * {
    margin-right: 0.5rem;
}

.gap-2 > *:last-child {
    margin-right: 0;
}
</style>
