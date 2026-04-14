<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Forms\HostProtectionPlanForm;
use Botble\CarRentals\Http\Requests\HostProtectionPlanRequest;
use Botble\CarRentals\Models\HostProtectionPlan;
use Botble\CarRentals\Tables\HostProtectionPlanTable;
use Exception;
use Illuminate\Http\Request;

class HostProtectionPlanController extends BaseController
{
    public function index(HostProtectionPlanTable $table)
    {
        PageTitle::setTitle('Host Protection Plans');

        return $table->renderTable();
    }

    public function create()
    {
        PageTitle::setTitle('Create Host Plan');

        return HostProtectionPlanForm::create()->renderForm();
    }

    public function store(HostProtectionPlanRequest $request, BaseHttpResponse $response)
    {
        $plan = HostProtectionPlan::query()->create($request->validated());

        event(new CreatedContentEvent(HOST_PROTECTION_PLAN_MODULE_SCREEN_NAME, $request, $plan));

        return $response
            ->setPreviousUrl(route('car-rentals.host-protection-plans.index'))
            ->setNextUrl(route('car-rentals.host-protection-plans.edit', $plan->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(HostProtectionPlan $plan)
    {
        PageTitle::setTitle('Edit Host Plan: ' . $plan->name);

        return HostProtectionPlanForm::createFromModel($plan)->renderForm();
    }

    public function update(HostProtectionPlan $plan, HostProtectionPlanRequest $request, BaseHttpResponse $response)
    {
        $plan->fill($request->validated());
        $plan->save();

        event(new UpdatedContentEvent(HOST_PROTECTION_PLAN_MODULE_SCREEN_NAME, $request, $plan));

        return $response
            ->setPreviousUrl(route('car-rentals.host-protection-plans.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(HostProtectionPlan $plan, Request $request, BaseHttpResponse $response)
    {
        try {
            $plan->delete();

            event(new DeletedContentEvent(HOST_PROTECTION_PLAN_MODULE_SCREEN_NAME, $request, $plan));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}