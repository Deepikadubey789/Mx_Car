@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <x-core::icon name="ti ti-calendar-check" />
                        {{ __('Car Availability Calendar') }}
                    </h4>
                </div>
                <div class="card-body">
                    <car-availability-calendar-component
                        v-cloak
                        :events-url="'{{ route('car-rentals.vendor.car-availability.events') }}'"
                        :availability-url="''"
                        :booking-details-url="'{{ route('car-rentals.vendor.car-availability.booking-details') }}'"
                        :cars="{{ $cars->toJson() }}"
                    ></car-availability-calendar-component>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <x-core::icon name="ti ti-info-circle" />
                        {{ __('How to Use Your Car Availability Calendar') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><x-core::icon name="ti ti-calendar-event" /> {{ __('Managing Your Bookings') }}</h6>
                            <ul class="list-unstyled">
                                <li><x-core::icon name="ti ti-point" /> {{ __('View all your car bookings in calendar format') }}</li>
                                <li><x-core::icon name="ti ti-point" /> {{ __('Click on any booking to see customer details') }}</li>
                                <li><x-core::icon name="ti ti-point" /> {{ __('Filter by specific cars to focus on individual vehicles') }}</li>
                                <li><x-core::icon name="ti ti-point" /> {{ __('Switch between month, week, and list views') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><x-core::icon name="ti ti-calendar-check" /> {{ __('Planning & Optimization') }}</h6>
                            <ul class="list-unstyled">
                                <li><x-core::icon name="ti ti-point" /> {{ __('Identify busy periods for your cars') }}</li>
                                <li><x-core::icon name="ti ti-point" /> {{ __('Plan maintenance during low-demand periods') }}</li>
                                <li><x-core::icon name="ti ti-point" /> {{ __('Track booking patterns and trends') }}</li>
                                <li><x-core::icon name="ti ti-point" /> {{ __('Optimize pricing based on availability') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><x-core::icon name="ti ti-palette" /> {{ __('Booking Status Colors') }}</h6>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge badge-warning">
                                    <x-core::icon name="ti ti-clock" /> {{ __('Pending Bookings') }}
                                </span>
                                <span class="badge badge-info">
                                    <x-core::icon name="ti ti-refresh" /> {{ __('Processing Bookings') }}
                                </span>
                                <span class="badge badge-success">
                                    <x-core::icon name="ti ti-check" /> {{ __('Completed Bookings') }}
                                </span>
                                <span class="badge badge-danger">
                                    <x-core::icon name="ti ti-x" /> {{ __('Cancelled Bookings') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><x-core::icon name="bulb" /> {{ __('Pro Tips') }}</h6>
                                <ul class="mb-0">
                                    <li>{{ __('Use the car filter to focus on specific vehicles when managing large fleets') }}</li>
                                    <li>{{ __('Click on bookings to quickly access customer information and booking details') }}</li>
                                    <li>{{ __('Monitor booking density to identify your most popular cars') }}</li>
                                    <li>{{ __('Plan car maintenance during gaps in bookings to maximize revenue') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <style>
        .fc-event {
            cursor: pointer;
            font-size: 0.85em;
        }

        .fc-event:hover {
            opacity: 0.8;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }

        .fc-daygrid-event {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-radius: 4px;
        }

        .fc-toolbar-title {
            font-size: 1.5em;
            font-weight: 600;
            color: var(--bs-primary);
        }

        .fc-button-group .fc-button {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            font-size: 0.9em;
        }

        .fc-button-group .fc-button:hover {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            opacity: 0.9;
        }

        .fc-button-group .fc-button-active {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        .fc-select-bg {
            background-color: rgba(var(--bs-primary-rgb), 0.2);
        }

        .car-availability-calendar .table th {
            background-color: var(--bs-light);
            font-weight: 600;
        }

        .car-availability-calendar .badge {
            font-size: 0.75em;
            padding: 0.25em 0.5em;
        }

        .tooltip-inner {
            max-width: 300px;
            text-align: left;
        }

        .fc-daygrid-day-number {
            color: var(--bs-dark);
            font-weight: 500;
        }

        .fc-day-today {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        }

        .fc-day-today .fc-daygrid-day-number {
            background-color: var(--bs-primary);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2px;
        }

        .alert-info {
            border-left: 4px solid var(--bs-info);
        }

        .card-header {
            background-color: var(--bs-light);
            border-bottom: 1px solid var(--bs-border-color);
        }
    </style>
@endpush
