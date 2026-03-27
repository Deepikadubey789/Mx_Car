<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Events\BookingStatusChanged;
use Botble\CarRentals\Http\Resources\BookingDetailResource;
use Botble\CarRentals\Http\Resources\BookingResource;
use Botble\CarRentals\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends BaseApiController
{
    /**
     * List vendor bookings
     *
     * @group Car Rentals - Vendor
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $query = Booking::query()
            ->where('vendor_id', $vendor->id)
            ->with(['car.car', 'services', 'currency', 'payment', 'customer'])->latest();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('booking_number', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $perPage = min($request->integer('per_page', 10), 50);
        $bookings = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(BookingResource::collection($bookings))
            ->toApiResponse();
    }

    /**
     * Get booking details
     *
     * @group Car Rentals - Vendor
     */
    public function show(int $id)
    {
        $vendor = Auth::guard('sanctum')->user();

        $booking = Booking::query()
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->with(['car.car', 'services', 'currency', 'payment', 'customer', 'invoice'])
            ->first();

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData(new BookingDetailResource($booking))
            ->toApiResponse();
    }

    /**
     * Update booking status
     *
     * @group Car Rentals - Vendor
     */
    public function updateStatus(int $id, Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $booking = Booking::query()
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        $request->validate([
            'status' => ['required', 'in:pending,confirmed,in_progress,completed,cancelled'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $request->input('status');
        $oldStatus = $booking->status;

        // Validate status transitions
        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (! in_array($newStatus, $allowedTransitions[$oldStatus->value] ?? [])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Invalid status transition')
                ->toApiResponse();
        }

        try {
            $updateData = ['status' => $newStatus];

            if ($request->has('note')) {
                $updateData['note'] = $booking->note . "\n\nVendor note: " . $request->input('note');
            }

            $booking->update($updateData);

            event(new BookingStatusChanged($booking, $oldStatus, $newStatus));

            $booking->load(['car.car', 'services', 'currency', 'payment', 'customer']);

            return $this
                ->httpResponse()
                ->setData(new BookingDetailResource($booking))
                ->setMessage('Booking status updated successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Complete booking
     *
     * @group Car Rentals - Vendor
     */
    public function complete(int $id, Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $booking = Booking::query()
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        if ($booking->status !== BookingStatusEnum::IN_PROGRESS) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Booking must be in progress to complete')
                ->toApiResponse();
        }

        $request->validate([
            'completion_miles' => ['nullable', 'numeric', 'min:0'],
            'completion_gas_level' => ['nullable', 'string', 'max:50'],
            'completion_damage_images' => ['nullable', 'array'],
            'completion_damage_images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'completion_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $updateData = [
                'status' => BookingStatusEnum::COMPLETED,
                'completion_miles' => $request->input('completion_miles'),
                'completion_gas_level' => $request->input('completion_gas_level'),
                'completion_notes' => $request->input('completion_notes'),
                'completed_at' => now(),
            ];

            // Handle damage images upload
            if ($request->hasFile('completion_damage_images')) {
                $damageImages = [];
                foreach ($request->file('completion_damage_images') as $image) {
                    $result = RvMedia::handleUpload($image, 0, 'bookings/damage');
                    if (! $result['error']) {
                        $damageImages[] = $result['data']->url;
                    }
                }
                $updateData['completion_damage_images'] = $damageImages;
            }

            $booking->update($updateData);

            event(new BookingStatusChanged($booking, BookingStatusEnum::IN_PROGRESS, BookingStatusEnum::COMPLETED));

            $booking->load(['car.car', 'services', 'currency', 'payment', 'customer']);

            return $this
                ->httpResponse()
                ->setData(new BookingDetailResource($booking))
                ->setMessage('Booking completed successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }
}
