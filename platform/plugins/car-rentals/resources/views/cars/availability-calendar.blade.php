@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <x-core::icon name="ti ti-calendar-check" class="me-2" />
                        {{ trans('plugins/car-rentals::car-rentals.availability_calendar.title') }}
                    </h2>
                    <div class="text-secondary mt-1">
                        {{ trans('plugins/car-rentals::car-rentals.availability_calendar.description') }}
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('car-rentals.cars.index') }}" class="btn">
                            <x-core::icon name="ti ti-car" class="me-1" />
                            {{ trans('plugins/car-rentals::car-rentals.car.name') }}
                        </a>
                        <a href="{{ route('car-rentals.bookings.index') }}" class="btn">
                            <x-core::icon name="ti ti-calendar-event" class="me-1" />
                            {{ trans('plugins/car-rentals::booking.name') }}
                        </a>
                        <a href="{{ route('car-rentals.cars.create') }}" class="btn btn-primary">
                            <x-core::icon name="ti ti-plus" class="me-1" />
                            {{ trans('plugins/car-rentals::car-rentals.car.create') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <car-availability-calendar-component
                        v-cloak
                        :events-url="'{{ route('car-rentals.car-availability.events') }}'"
                        :availability-url="'{{ route('car-rentals.car-availability.status') }}'"
                        :booking-details-url="'{{ route('car-rentals.car-availability.booking-details') }}'"
                        :cars="{{ $cars->toJson() }}"
                    ></car-availability-calendar-component>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <style>
        /* Page Header Enhancements */
        .page-header {
            margin-bottom: 2rem;
        }

        /* Card Enhancements */
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        /* Improved spacing */
        .container-xl {
            max-width: 1320px;
        }
    </style>
@endpush
