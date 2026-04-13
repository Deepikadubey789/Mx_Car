<?php

namespace Botble\CarRentals\Http\Controllers\Api;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Http\Resources\GuestProtectionPlanResource;
use Botble\CarRentals\Http\Resources\HostProtectionPlanResource;
use Botble\CarRentals\Models\GuestProtectionPlan;
use Botble\CarRentals\Models\HostProtectionPlan;
use Illuminate\Http\JsonResponse;

class ProtectionPlanController extends BaseController
{
    /**
     * Get all active Guest Protection Plans
     * (Useful for the customer app checkout screen)
     */
    public function getGuestPlans(): JsonResponse
    {
        $plans = GuestProtectionPlan::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->get();

        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => GuestProtectionPlanResource::collection($plans),
        ]);
    }

    /**
     * Get all active Host Protection Plans
     * (Useful for the Vendor app when listing a new car)
     */
    public function getHostPlans(): JsonResponse
    {
        $plans = HostProtectionPlan::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->get();

        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => HostProtectionPlanResource::collection($plans),
        ]);
    }
}