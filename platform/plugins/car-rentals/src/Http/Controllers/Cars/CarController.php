<?php

namespace Botble\CarRentals\Http\Controllers\Cars;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Enums\CarDateValueTypeEnum;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Forms\CarForm;
use Botble\CarRentals\Http\Requests\CarRequest;
use Botble\CarRentals\Http\Requests\CarAutoApplySettingsRequest;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarDate;
use Botble\CarRentals\Models\DemandPricingRecommendation;
use Botble\CarRentals\Models\CarPricingPolicy;
use Botble\CarRentals\Models\CarTripDiscount;
use Botble\CarRentals\Models\CarTag;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Services\DemandPricingRecommendationService;
use Botble\CarRentals\Tables\CarTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class CarController extends BaseController
{
    public function __construct()
    {
        $this->breadcrumb()
            ->add(trans('plugins/car-rentals::car-rentals.name'))
            ->add(trans('plugins/car-rentals::car-rentals.car.name'), route('car-rentals.cars.index'));
    }

    public function index(CarTable $table)
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.car.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.car.create'));

        return CarForm::create()->renderForm();
    }

    public function store(CarRequest $request)
    {
        $form = CarForm::create()->setRequest($request);
        $form->saving(function (CarForm $form) use ($request): void {
            /**
             * @var Car $model
             */
            $model = $form->getModel();

            $dataCreate = $request->validated();

            // Removed is_same_drop_off logic as these fields are now customer-selected

            if ($request->input('author_id')) {
                $dataCreate['author_type'] = Customer::class;
            }

            $model->fill($dataCreate);

            $model->images = array_filter($request->input('images', []));
            $model->moderation_status = ModerationStatusEnum::APPROVED;

            $model->save();

            $this->syncPricingPolicy($model, $request);

            $tags = $request->input('tags');

            $tags = $tags ? explode(',', $tags) : [];

            $tagIds = CarTag::query()->wherePublished()->whereIn('id', $tags)->pluck('id')->all();

            if ($tagIds) {
                $model->tags()->sync($tagIds);
            }

            $model->categories()->sync($request->input('categories', []));

            $colors = $request->input('colors');

            $colors = $colors ? explode(',', $colors) : [];

            if ($colors) {
                $model->colors()->sync($colors);
            }

            $model->amenities()->sync($request->input('amenities', []));
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.cars.index'))
            ->setNextUrl(route('car-rentals.cars.edit', $form->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(Car $car)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $car->name]));

        return CarForm::createFromModel($car)->renderForm();
    }

    public function update(Car $car, CarRequest $request)
    {
        CarForm::createFromModel($car)->saving(function (CarForm $form) use ($request): void {
            /**
             * @var Car $model
             */
            $model = $form->getModel();
            $dataUpdate = $request->validated();
            // Removed is_same_drop_off logic as these fields are now customer-selected

            if ($request->input('author_id')) {
                $dataUpdate['author_type'] = Customer::class;
            }

            $model->fill($dataUpdate);
            $model->images = array_filter($request->input('images', []));

            $model->save();

            $this->syncPricingPolicy($model, $request);

            $tags = $request->input('tags');

            $tags = $tags ? explode(',', $tags) : [];

            $tagIds = CarTag::query()->wherePublished()->whereIn('id', $tags)->pluck('id')->all();

            $model->tags()->sync($tagIds);

            $model->categories()->sync($request->input('categories', []));

            $colors = $request->input('colors');

            $colors = $colors ? explode(',', $colors) : [];

            $model->colors()->sync($colors);

            $model->amenities()->sync($request->input('amenities', []));
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.cars.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Car $car): DeleteResourceAction
    {
        return DeleteResourceAction::make($car);
    }

    public function applyDemandPricingRecommendation(Car $car, DemandPricingRecommendation $recommendation)
    {
        abort_unless($recommendation->car_id === $car->getKey(), 404);
        abort_unless($recommendation->status === 'pending', 422);

        $carDate = app(DemandPricingRecommendationService::class)->applyRecommendation($recommendation, auth()->id());

        return $this
            ->httpResponse()
            ->setData([
                'car_date' => $carDate,
                'recommendation_id' => $recommendation->getKey(),
            ])
            ->setMessage(__('Demand pricing recommendation applied successfully.'));
    }

    public function dismissDemandPricingRecommendation(Car $car, DemandPricingRecommendation $recommendation)
    {
        abort_unless($recommendation->car_id === $car->getKey(), 404);
        abort_unless($recommendation->status === 'pending', 422);

        app(DemandPricingRecommendationService::class)->dismissRecommendation($recommendation, auth()->id());

        return $this
            ->httpResponse()
            ->setMessage(__('Demand pricing recommendation dismissed.'));
    }

    public function updateAutoApplySettings(Car $car, CarAutoApplySettingsRequest $request)
    {
        $this->authorize('update', $car);

        $policy = $car->pricingPolicy()->firstOrNew([]);
        $policy->fill([
            'demand_auto_apply_enabled' => $request->boolean('demand_auto_apply_enabled', $policy->demand_auto_apply_enabled ?? false),
            'demand_auto_apply_min_confidence' => $request->input('demand_auto_apply_min_confidence', $policy->demand_auto_apply_min_confidence ?? 0.70),
            'demand_auto_apply_max_daily_change_percent' => $request->input('demand_auto_apply_max_daily_change_percent', $policy->demand_auto_apply_max_daily_change_percent),
        ])->save();

        // Handle pause request
        if ($request->input('demand_auto_apply_pause_hours')) {
            app(\Botble\CarRentals\Services\AutoApplyQueueService::class)
                ->pauseAutoApply($car, (int) $request->input('demand_auto_apply_pause_hours'));
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Auto-apply settings updated successfully.'));
    }

    protected function syncPricingPolicy(Car $car, Request $request): void
    {
        $policyKeys = [
            'weekly_discount_type',
            'weekly_discount_value',
            'monthly_discount_type',
            'monthly_discount_value',
            'included_distance_per_day',
            'included_distance_per_trip',
            'extra_distance_unit_price',
            'distance_unit',
            'distance_overage_billing_mode',
            'allow_best_discount_only',
            'max_discount_cap_percent',
                'demand_recommendations_enabled',
                'demand_min_price',
                'demand_max_price',
                'demand_max_daily_change_percent',
                'demand_auto_apply_enabled',
                'demand_auto_apply_min_confidence',
                'demand_auto_apply_max_daily_change_percent',
        ];

        $hasPolicyInput = false;

        foreach ($policyKeys as $key) {
            if ($request->has($key)) {
                $hasPolicyInput = true;
                break;
            }
        }

        $tripDiscounts = $request->input('trip_discounts');
        if (! $hasPolicyInput && ! is_array($tripDiscounts)) {
            return;
        }

        $policy = $car->pricingPolicy()->firstOrNew([]);
        $policy->fill([
            'weekly_discount_type' => $request->input('weekly_discount_type', $policy->weekly_discount_type ?? 'none'),
            'weekly_discount_value' => $request->input('weekly_discount_value', $policy->weekly_discount_value ?? 0),
            'monthly_discount_type' => $request->input('monthly_discount_type', $policy->monthly_discount_type ?? 'none'),
            'monthly_discount_value' => $request->input('monthly_discount_value', $policy->monthly_discount_value ?? 0),
            'included_distance_per_day' => $request->input('included_distance_per_day', $policy->included_distance_per_day),
            'included_distance_per_trip' => $request->input('included_distance_per_trip', $policy->included_distance_per_trip),
            'extra_distance_unit_price' => $request->input('extra_distance_unit_price', $policy->extra_distance_unit_price ?? 0),
            'distance_unit' => $request->input('distance_unit', $policy->distance_unit ?? 'km'),
            'distance_overage_billing_mode' => $request->input('distance_overage_billing_mode', $policy->distance_overage_billing_mode ?? 'end_of_trip'),
            'allow_best_discount_only' => $request->boolean('allow_best_discount_only', $policy->allow_best_discount_only ?? true),
            'max_discount_cap_percent' => $request->input('max_discount_cap_percent', $policy->max_discount_cap_percent),
                'demand_recommendations_enabled' => $request->boolean('demand_recommendations_enabled', $policy->demand_recommendations_enabled ?? false),
                'demand_min_price' => $request->input('demand_min_price', $policy->demand_min_price),
                'demand_max_price' => $request->input('demand_max_price', $policy->demand_max_price),
                'demand_max_daily_change_percent' => $request->input('demand_max_daily_change_percent', $policy->demand_max_daily_change_percent),
                'demand_auto_apply_enabled' => $request->boolean('demand_auto_apply_enabled', $policy->demand_auto_apply_enabled ?? false),
                'demand_auto_apply_min_confidence' => $request->input('demand_auto_apply_min_confidence', $policy->demand_auto_apply_min_confidence ?? 0.70),
                'demand_auto_apply_max_daily_change_percent' => $request->input('demand_auto_apply_max_daily_change_percent', $policy->demand_auto_apply_max_daily_change_percent),
            'active' => true,
        ]);
        $policy->car_id = $car->getKey();
        $policy->save();

        if (is_array($tripDiscounts)) {
            $policy->tripDiscounts()->delete();

            foreach ($tripDiscounts as $tripDiscount) {
                if (! is_array($tripDiscount)) {
                    continue;
                }

                $minDays = (int) Arr::get($tripDiscount, 'min_days', 0);
                $discountValue = Arr::get($tripDiscount, 'discount_value');

                if ($minDays < 1 || $discountValue === null || $discountValue === '') {
                    continue;
                }

                $policy->tripDiscounts()->create([
                    'car_id' => $car->getKey(),
                    'min_days' => $minDays,
                    'max_days' => Arr::get($tripDiscount, 'max_days'),
                    'discount_type' => Arr::get($tripDiscount, 'discount_type', 'percentage'),
                    'discount_value' => $discountValue,
                    'priority' => Arr::get($tripDiscount, 'priority', 0),
                    'active' => (bool) Arr::get($tripDiscount, 'active', true),
                    'description' => Arr::get($tripDiscount, 'description'),
                ]);
            }
        }
    }

    public function approve(Car $car)
    {
        abort_unless($car->is_pending_moderation, 404);

        $car->moderation_status = ModerationStatusEnum::APPROVED;
        $car->save();

        EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'author_name' => $car->author->name,
                'car_name' => $car->name,
                'car_link' => route('car-rentals.vendor.cars.edit', $car->getKey()),
            ])
            ->sendUsingTemplate('car-approved', $car->author->email);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.cars.index'))
            ->setMessage(trans('plugins/car-rentals::car-rentals.car.forms.status_moderation.approved'));
    }

    public function reject(Car $car, Request $request)
    {
        abort_unless($car->is_pending_moderation, 404);

        $request->validate([
            'reason' => ['required', 'string', 'max:400'],
        ]);

        $car->moderation_status = ModerationStatusEnum::REJECTED;
        $car->reject_reason = $request->input('reason');
        $car->save();

        EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'author_name' => $car->author->name,
                'car_name' => $car->name,
                'car_link' => route('car-rentals.vendor.cars.edit', $car->getKey()),
                'reason' => $request->input('reason'),
            ])
            ->sendUsingTemplate('car-rejected', $car->author->email);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.cars.index'))
            ->setMessage(trans('plugins/car-rentals::car-rentals.car.forms.status_moderation.rejected'));
    }

    public function getCarPricing(Car $car, Request $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $startDate = Carbon::parse($request->input('start'));
        $endDate = Carbon::parse($request->input('end'));

        $events = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $events[$dateStr] = [
                'id' => null,
                'title' => format_price($car->rental_rate),
                'start' => $dateStr,
                'end' => $dateStr,
                'value' => $car->rental_rate,
                'value_type' => CarDateValueTypeEnum::FIXED,
                'active' => 1,
                'backgroundColor' => '#e9ecef',
                'textColor' => '#495057',
                'classNames' => ['base-price'],
            ];
        }

        $carDates = CarDate::query()
            ->where('car_id', $car->getKey())
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get();

        foreach ($carDates as $carDate) {
            $dateStr = $carDate->start_date->format('Y-m-d');
            $displayPrice = $this->calculateDisplayPrice($car, $carDate);

            $events[$dateStr] = [
                'id' => $carDate->getKey(),
                'title' => format_price($displayPrice),
                'start' => $dateStr,
                'end' => $dateStr,
                'value' => $carDate->value,
                'value_type' => $carDate->value_type->getValue(),
                'active' => $carDate->active,
                'backgroundColor' => $carDate->active ? $this->getPriceBackgroundColor($carDate) : '#6c757d',
                'textColor' => '#ffffff',
                'classNames' => $carDate->active ? ['custom-price'] : ['custom-price', 'inactive'],
            ];
        }

        $recommendationEvents = app(DemandPricingRecommendationService::class)->getCalendarEvents($car, $startDate, $endDate);

        foreach ($recommendationEvents as $recommendationEvent) {
            $events[$recommendationEvent['id']] = $recommendationEvent;
        }

        return response()->json(array_values($events));
    }

    public function storeCarPricing(Car $car, Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'value' => ['nullable', 'numeric'],
            'value_type' => ['required', Rule::in(CarDateValueTypeEnum::values())],
            'active' => ['required', 'boolean'],
        ]);

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');

            $carDate = CarDate::query()
                ->where('car_id', $car->getKey())
                ->whereDate('start_date', $dateStr)
                ->first();

            if (! $carDate) {
                $carDate = new CarDate();
                $carDate->car_id = $car->getKey();
            }

            $carDate->fill([
                'start_date' => $dateStr,
                'end_date' => $dateStr,
                'value' => $request->input('value') ?? 0,
                'value_type' => $request->input('value_type'),
                'active' => $request->boolean('active'),
            ]);

            $carDate->save();

            DemandPricingRecommendation::query()
                ->where('car_id', $car->getKey())
                ->whereDate('recommendation_date', $dateStr)
                ->where('status', 'pending')
                ->update([
                    'status' => 'dismissed',
                ]);
        }

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage();
    }

    protected function calculateDisplayPrice(Car $car, CarDate $carDate): float
    {
        return match ($carDate->value_type->getValue()) {
            CarDateValueTypeEnum::FIXED => $carDate->value,
            CarDateValueTypeEnum::AMOUNT_ADJUST => $car->rental_rate + $carDate->value,
            CarDateValueTypeEnum::PERCENTAGE_ADJUST => $car->rental_rate + ($car->rental_rate * $carDate->value / 100),
            default => $car->rental_rate,
        };
    }

    protected function getPriceBackgroundColor(CarDate $carDate): string
    {
        return match ($carDate->value_type->getValue()) {
            CarDateValueTypeEnum::FIXED => '#206bc4',
            CarDateValueTypeEnum::AMOUNT_ADJUST => '#2fb344',
            CarDateValueTypeEnum::PERCENTAGE_ADJUST => '#f76707',
            default => '#206bc4',
        };
    }
}
