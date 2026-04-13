<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Forms\GuestProtectionPlanForm;
use Botble\CarRentals\Http\Requests\GuestProtectionPlanRequest;
use Botble\CarRentals\Models\GuestProtectionPlan;
use Botble\CarRentals\Tables\GuestProtectionPlanTable;
use Exception;
use Illuminate\Http\Request;

class GuestProtectionPlanController extends BaseController
{
    public function index(GuestProtectionPlanTable $table)
    {
        PageTitle::setTitle('Guest Protection Plans');

        return $table->renderTable();
    }

    public function create()
    {
        PageTitle::setTitle('Create Guest Plan');

        return GuestProtectionPlanForm::create()->renderForm();
    }

    public function store(GuestProtectionPlanRequest $request, BaseHttpResponse $response)
    {
        $plan = GuestProtectionPlan::query()->create($request->validated());

        event(new CreatedContentEvent(GUEST_PROTECTION_PLAN_MODULE_SCREEN_NAME, $request, $plan));

        return $response
            ->setPreviousUrl(route('car-rentals.guest-protection-plans.index'))
            ->setNextUrl(route('car-rentals.guest-protection-plans.edit', $plan->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(GuestProtectionPlan $plan)
    {
        PageTitle::setTitle('Edit Guest Plan: ' . $plan->name);

        return GuestProtectionPlanForm::createFromModel($plan)->renderForm();
    }

    public function update(GuestProtectionPlan $plan, GuestProtectionPlanRequest $request, BaseHttpResponse $response)
    {
        $plan->fill($request->validated());
        $plan->save();

        event(new UpdatedContentEvent(GUEST_PROTECTION_PLAN_MODULE_SCREEN_NAME, $request, $plan));

        return $response
            ->setPreviousUrl(route('car-rentals.guest-protection-plans.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(GuestProtectionPlan $plan, Request $request, BaseHttpResponse $response)
    {
        try {
            $plan->delete();

            event(new DeletedContentEvent(GUEST_PROTECTION_PLAN_MODULE_SCREEN_NAME, $request, $plan));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}