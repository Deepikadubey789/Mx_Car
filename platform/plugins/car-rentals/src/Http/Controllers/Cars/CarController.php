<?php

namespace Botble\CarRentals\Http\Controllers\Cars;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Enums\CarDateValueTypeEnum;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Forms\CarForm;
use Botble\CarRentals\Http\Requests\CarRequest;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarDate;
use Botble\CarRentals\Models\CarTag;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Tables\CarTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
