@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <div class="section-content">
        <div class="mb-4">

            @if (count($reviews) > 0)
                <div class="row">
                    @foreach ($reviews as $review)
                        @php
                        $car = $review->car;
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="bb-customer-card">
                                <div class="bb-card-image">
                                    <a href="{{ $car->url }}" target="_blank">
                                        <img src="{{ RvMedia::getImageUrl($car->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                             alt="{{ $car->name }}">
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="bb-card-meta">
                                        <div class="bb-card-date">
                                            <x-core::icon name="ti ti-calendar" />
                                            {{ $review->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <h5 class="bb-card-title">
                                        <a href="{{ $car->url }}" target="_blank">{{ $car->name }}</a>
                                    </h5>
                                    <div class="bb-card-rating">
                                        <div class="stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <x-core::icon
                                                    name="ti ti-star{{ $i <= $review->star ? '-filled' : '' }}"
                                                    class="{{ $i <= $review->star ? 'text-warning' : 'text-muted' }}" />
                                            @endfor
                                        </div>
                                        <span class="rating-text ms-1">({{ $review->star }})</span>
                                    </div>
                                    <div class="bb-card-content mt-3">
                                        <p>{{ $review->content }}</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-primary btn-sm flex-fill" href="{{ $car->url }}#reviews" target="_blank">
                                            <x-core::icon name="ti ti-car" class="me-1" />
                                            {{ __('View Car') }}
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-review-btn" 
                                                data-review-id="{{ $review->id }}"
                                                data-car-name="{{ $car->name }}"
                                                type="button">
                                            <x-core::icon name="ti ti-trash" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {!! $reviews->withQueryString()->links(CarRentalsHelper::viewPath('partials.pagination')) !!}
            @else
                <div class="bb-empty-state">
                    <x-core::icon name="ti ti-message-off" size="lg" />
                    <h4>{{ __('No Reviews Yet') }}</h4>
                    <p>{{ __("You haven't written any reviews yet. After completing a booking, you can share your experience by writing a review.") }}</p>

                    @php
                        $hasBookings = \Botble\CarRentals\Models\Booking::query()
                            ->where('customer_id', auth('customer')->id())
                            ->exists();
                    @endphp

                    @if ($hasBookings)
                        <a href="{{ route('customer.bookings') }}" class="btn btn-primary m-auto">
                            <x-core::icon name="ti ti-calendar" class="me-1" />
                            {{ __('View My Bookings') }}
                        </a>
                    @else
                        <div class="text-center">
                            <a href="{{ route('public.cars') }}" class="btn btn-primary d-inline-block m-auto">
                                <x-core::icon name="ti ti-car" class="me-1" />
                                {{ __('Explore Cars') }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    @if (count($reviews) > 0)
        <script>
            window.reviewTranslations = {
                deleteReview: '{{ __("Delete Review") }}',
                confirmDelete: '{{ __("Are you sure you want to delete your review for") }}',
                cannotUndo: '{{ __("This action cannot be undone.") }}',
                cancel: '{{ __("Cancel") }}',
                deleting: '{{ __("Deleting...") }}',
                deleteSuccess: '{{ __("Review deleted successfully!") }}',
                deleteFailed: '{{ __("Failed to delete review") }}',
                deleteError: '{{ __("An error occurred while deleting the review") }}',
                deleteUrl: '{{ url("reviews") }}',
                csrfToken: '{{ csrf_token() }}',
                trashIcon: `{!! BaseHelper::renderIcon('ti ti-trash', 'text-danger', ['style' => 'font-size: 32px;']) !!}`,
                trashIconSmall: `{!! BaseHelper::renderIcon('ti ti-trash', 'me-1') !!}`
            };
        </script>
        @php
            Theme::asset()->container('footer')->usePath()->add('customer-reviews-js', 'js/customer-reviews.js');
        @endphp
    @endif
@endsection
