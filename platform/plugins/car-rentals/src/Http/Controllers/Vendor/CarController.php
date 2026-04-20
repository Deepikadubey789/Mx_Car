<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Forms\Vendor\CarForm;
use Botble\CarRentals\Http\Requests\Vendor\CarRequest;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarTag;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Services\VendorDemandPricingService;
use Botble\CarRentals\Tables\Vendor\CarTable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CarController extends BaseController
{
    public function index(CarTable $carTable)
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.car.name'));

        return $carTable->render('plugins/car-rentals::themes.vendor-dashboard.table.base');
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.car.create'));

        return CarForm::create()
            ->renderForm();
    }

   public function store(CarRequest $request)
    {
        $carForm = CarForm::create()->setRequest($request);

        $carForm->saving(function (CarForm $form): void {
            $request = $form->getRequest();

            /**
             * @var Car $car
             */
            $car = $form->getModel();

            $car->fill(array_merge($this->processRequestData($request), [
                'author_id' => auth('customer')->id(),
                'author_type' => Customer::class,
                // NEW: Handle boolean and inputs for delivery
                'is_delivery_enabled' => $request->has('is_delivery_enabled'),
                'free_delivery_days_threshold' => $request->input('free_delivery_days_threshold'),
                'max_delivery_distance_miles' => $request->input('max_delivery_distance_miles'),
            ]));

            if (! CarRentalsHelper::isEnabledPostApproval()) {
                $car->moderation_status = ModerationStatusEnum::APPROVED;
            }

            $car->save();

            $this->syncPricingPolicy($car, $request);

            // Sync relationships
            $tags = $request->input('tags');
            $tags = $tags ? explode(',', $tags) : [];
            $tagIds = CarTag::query()->wherePublished()->whereIn('id', $tags)->pluck('id')->all();
            $car->tags()->sync($tagIds);

            $car->categories()->sync($request->input('categories', []));

            $colors = $request->input('colors');
            $colors = $colors ? explode(',', $colors) : [];
            $car->colors()->sync($colors);

            $car->amenities()->sync($request->input('amenities', []));

            // NEW: Sync Delivery Locations Pivot Table
            if ($request->has('delivery_locations')) {
                $car->deliveryLocations()->sync($request->input('delivery_locations'));
            }

            $form->fireModelEvents($car);

            EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'post_name' => $car->name,
                    'post_url' => route('car-rentals.cars.edit', $car->getKey()),
                    'post_author' => $car->author->name,
                ])
                ->sendUsingTemplate('new-pending-car');
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.vendor.cars.index'))
            ->setNextUrl(route('car-rentals.vendor.cars.edit', $carForm->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(Car $car, VendorDemandPricingService $vendorPricingService)
    {
        abort_if($car->author_type != Customer::class || $car->author_id != auth('customer')->id(), 403);

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $car->name]));

        $vendorId = auth('customer')->id();
        $grouped = $vendorPricingService->getRecommendationsByCar($vendorId, $car->id, 'pending');
        $recommendationsForCar = $grouped->get($car->id, collect())->values();

        // Share view data for the form template
        view()->share([
            'carRecommendations' => $recommendationsForCar,
            'carRecommendationCount' => $recommendationsForCar->count(),
        ]);

        return CarForm::createFromModel($car)->renderForm();
    }

    public function update(Car $car, CarRequest $request)
    {
        abort_if($car->author_type != Customer::class || $car->author_id != auth('customer')->id(), 403);

        $carForm = CarForm::createFromModel($car)->setRequest($request);

        $carForm->saving(function (CarForm $form): void {
            $request = $form->getRequest();

            /**
             * @var Car $car
             */
            $car = $form->getModel();

            $carData = $this->processRequestData($request);
            // NEW: Handle boolean and inputs for delivery
            $carData['is_delivery_enabled'] = $request->has('is_delivery_enabled');
            $carData['free_delivery_days_threshold'] = $request->input('free_delivery_days_threshold');
            $carData['max_delivery_distance_miles'] = $request->input('max_delivery_distance_miles');

            $car->fill($carData);
            $car->save();

            // Sync relationships
            $tags = $request->input('tags');
            $tags = $tags ? explode(',', $tags) : [];
            $tagIds = CarTag::query()->wherePublished()->whereIn('id', $tags)->pluck('id')->all();
            $car->tags()->sync($tagIds);

            $car->categories()->sync($request->input('categories', []));

            $colors = $request->input('colors');
            $colors = $colors ? explode(',', $colors) : [];
            $car->colors()->sync($colors);

            $car->amenities()->sync($request->input('amenities', []));

            // NEW: Sync Delivery Locations Pivot Table
            if ($request->has('delivery_locations')) {
                $car->deliveryLocations()->sync($request->input('delivery_locations'));
            } else {
                $car->deliveryLocations()->detach();
            }

            $this->syncPricingPolicy($car, $request);

            $form->fireModelEvents($car);
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.vendor.cars.index'))
            ->setNextUrl(route('car-rentals.vendor.cars.edit', $car->id))
            ->withUpdatedSuccessMessage();
    }

    protected function processRequestData(Request $request): array
    {
        $shortcodeCompiler = shortcode()->getCompiler();

        $request->merge([
            'content' => $shortcodeCompiler->strip(
                $request->input('content'),
                $shortcodeCompiler->whitelistShortcodes()
            ),
        ]);

        $except = [
            'is_featured',
            'author_id',
            'author_type',
            'moderation_status',
        ];

        foreach ($except as $item) {
            $request->request->remove($item);
        }

        return $request->input();
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

    public function destroy(Car $car): DeleteResourceAction
    {
        abort_if($car->author_type != Customer::class || $car->author_id != auth('customer')->id(), 403);

        return DeleteResourceAction::make($car);
    }
}
