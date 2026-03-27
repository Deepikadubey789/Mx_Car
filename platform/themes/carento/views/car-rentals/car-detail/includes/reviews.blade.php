@if(CarRentalsHelper::isEnabledCarReviews())
    @php
        $overviewReviews = CarRentalsHelper::getReviewsGroupedByCarId($car->id, $car->reviews_count);
    @endphp
    <div class="group-collapse-expand">
        <button class="btn btn-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReviews" aria-expanded="false" aria-controls="collapseReviews">
            <strong class="heading-6">{{ __('Rate & Reviews') }}</strong>
            <x-core::icon name="ti ti-chevron-down" />
        </button>
        <div class="collapse show" id="collapseReviews">
            <div class="card card-body">
                @if($car->reviews_count > 0)
                    <div class="head-reviews">
                        <div class="review-left">
                            <div class="review-info-inner">
                                <strong class="heading-6 neutral-1000">{{ __(':avg_star/5', ['avg_star' => $reviewAvg = round($car->reviews_sum_star / ($car->reviews_count ?: 1), 2)]) }}</strong>
                                @if ($reviewCount = $car->reviews_count)
                                    <p class="text-sm-medium neutral-400">({{ __(':number reviews', ['number' => $reviewCount]) }})</p>
                                @endif

                                @include(CarRentalsHelper::viewPath('reviews.includes.rating-star'), ['avg' => $reviewAvg])
                            </div>
                        </div>

                        <div class="review-right">
                            <div class="review-progress">
                                @foreach($overviewReviews as $overviewReview)
                                    <div class="item-review-progress">
                                        <div class="text-rv-progress">
                                            <p class="text-sm-bold">{{ __(':number Star', ['number' => Arr::get($overviewReview, 'star')]) }}</p>
                                        </div>
                                        <div class="bar-rv-progress">
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ Arr::get($overviewReview, 'percent') }}%;"></div>
                                            </div>
                                        </div>
                                        <div class="text-avarage">
                                            <p>{{ number_format(Arr::get($overviewReview, 'count') ?: 0, 2) }}%</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @include(CarRentalsHelper::viewPath('reviews.includes.items'))

                    @if ($reviews instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $reviews->total() > 0)
                        {{ $reviews->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
                    @endif
                @else
                    <div class="empty-reviews-state text-center py-5">
                        <div class="empty-icon mb-3">
                            <x-core::icon name="ti ti-message-circle" size="64" class="text-muted" />
                        </div>
                        <h5 class="text-muted mb-2">{{ __('No reviews yet') }}</h5>
                        <p class="text-sm text-muted mb-0">{{ __('Be the first to share your experience with this car.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $customerHasReviewed = false;
        if(auth('customer')->check()) {
            $customerHasReviewed = \Botble\CarRentals\Models\CarReview::query()
                ->where('car_id', $car->id)
                ->where('customer_id', auth('customer')->id())
                ->exists();
        }
    @endphp
    
    <div class="group-collapse-expand">
        <button class="btn btn-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAddReview" aria-expanded="false" aria-controls="collapseAddReview">
            <strong class="heading-6">{{ $customerHasReviewed ? __('Your Review') : __('Add a review') }}</strong>
            <x-core::icon name="ti ti-chevron-down" />
        </button>
        <div class="collapse show" id="collapseAddReview">
            <div class="card card-body">
                @include(CarRentalsHelper::viewPath('reviews.form'))
            </div>
        </div>
    </div>
@endif
