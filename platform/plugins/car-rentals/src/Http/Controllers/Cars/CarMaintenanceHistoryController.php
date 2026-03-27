<?php

namespace Botble\CarRentals\Http\Controllers\Cars;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Forms\CarMaintenanceHistoryForm;
use Botble\CarRentals\Http\Requests\CarMaintenanceHistoryRequest;
use Botble\CarRentals\Models\CarMaintenanceHistory;

class CarMaintenanceHistoryController extends BaseController
{
    public function store(CarMaintenanceHistoryRequest $request): BaseHttpResponse
    {
        $form = CarMaintenanceHistoryForm::create()->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setNextUrl(route('car-rentals.cars.edit', $form->getModel()->car_id))
            ->withCreatedSuccessMessage();
    }

    public function edit(CarMaintenanceHistory $carMaintenanceHistory): string
    {
        return CarMaintenanceHistoryForm::createFromModel($carMaintenanceHistory)
            ->setUrl(route('car-rentals.car-maintenance-histories.update', $carMaintenanceHistory))
            ->renderForm();
    }

    public function update(CarMaintenanceHistory $carMaintenanceHistory, CarMaintenanceHistoryRequest $request): BaseHttpResponse
    {
        CarMaintenanceHistoryForm::createFromModel($carMaintenanceHistory)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setNextUrl(route('car-rentals.cars.edit', $carMaintenanceHistory->car_id))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(CarMaintenanceHistory $carMaintenanceHistory): DeleteResourceAction
    {
        return DeleteResourceAction::make($carMaintenanceHistory);
    }
}
