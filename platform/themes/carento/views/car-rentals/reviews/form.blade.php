<div class="box-form-reviews">
    @if(auth('customer')->check())
        @php
            $customer = auth('customer')->user();
            $hasReviewed = \Botble\CarRentals\Models\CarReview::query()
                ->where('car_id', $car->id)
                ->where('customer_id', $customer->id)
                ->exists();
        @endphp
        
        @if($hasReviewed)
            <div class="text-center py-4">
                <div class="mb-3">
                    <x-core::icon name="ti ti-circle-check" size="48" class="text-success" />
                </div>
                <h5 class="heading-6 neutral-1000 mb-2">{{ __('Thank you for your feedback!') }}</h5>
                <p class="text-sm-medium neutral-400 mb-0">{{ __('You have already shared your experience with this car. Your review helps other customers make informed decisions.') }}</p>
            </div>
        @else
            {!! \Botble\CarRentals\Forms\Fronts\ReviewForm::createFromArray(['car' => $car])->renderForm() !!}
        @endif
    @else
        <div class="text-center py-4">
            <div class="mb-3">
                <x-core::icon name="ti ti-lock" size="48" class="text-warning" />
            </div>
            <h5 class="heading-6 neutral-1000 mb-2">{{ __('Sign in to Share Your Experience') }}</h5>
            <p class="text-sm-medium neutral-400 mb-3">
                {{ __('We value your feedback! Please sign in to write a review and help other customers make informed decisions.') }}
            </p>
            <a href="{{ route('customer.login') }}" class="btn btn-primary rounded-pill px-4 m-auto">
                <x-core::icon name="ti ti-user" class="me-1" />
                {{ __('Sign In to Review') }}
            </a>
            <p class="text-sm neutral-400 mt-3 mb-0">
                {{ __("Don't have an account?") }} 
                <a href="{{ route('customer.register') }}" class="text-primary fw-medium">{{ __('Create one here') }}</a>
            </p>
        </div>
    @endif
</div>
