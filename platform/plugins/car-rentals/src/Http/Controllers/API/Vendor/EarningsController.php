<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends BaseApiController
{
    /**
     * Get vendor earnings list
     *
     * @group Car Rentals - Vendor
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $query = Revenue::query()
            ->where('customer_id', $vendor->id)
            ->with(['booking.car']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // Filter by revenue type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $perPage = min($request->integer('per_page', 15), 50);
        $earnings = $query->latest()->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData([
                'earnings' => $earnings->items(),
                'pagination' => [
                    'current_page' => $earnings->currentPage(),
                    'last_page' => $earnings->lastPage(),
                    'per_page' => $earnings->perPage(),
                    'total' => $earnings->total(),
                    'has_more' => $earnings->hasMorePages(),
                ],
            ])
            ->toApiResponse();
    }
}
