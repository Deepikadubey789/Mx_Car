<?php

namespace Botble\CarRentals\Forms;

use Botble\ACL\Models\User;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Http\Requests\UpdateBookingRequest;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingClaim;
use Botble\CarRentals\Services\TripTimelineBuilder;

class BookingForm extends FormAbstract
{
    public function setup(): void
    {
        /** @var Booking $booking */
        $booking = $this->getModel();

        $canViewCasefile = $booking?->id
            && auth()->check()
            && auth()->user()->hasPermission('car-rentals.bookings.edit');

        $tripTimelineContent = '';
        if ($canViewCasefile) {
            $booking->loadMissing([
                'tripMessages.sender',
                'supportActions.admin',
                'claims.assignee',
                'kycVerification.documents',
                'payment',
                'invoice',
                'customer',
                'vendor',
                'car',
            ]);
            $tripTimelineContent = view('plugins/car-rentals::bookings.trip-timeline', [
                'booking' => $booking,
                'timeline' => app(TripTimelineBuilder::class)->build($booking),
                'claims' => $booking->claims,
                'claimStatuses' => BookingClaim::STATUSES,
                'claimPriorities' => ['low', 'normal', 'high', 'critical'],
                'claimOutcomes' => BookingClaim::OUTCOME_ACTIONS,
                'assignees' => User::query()
                    ->select(['id', 'first_name', 'last_name', 'email'])
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->limit(100)
                    ->get(),
            ])->render();
        }

        $metaBoxes = [
            'information' => [
                'title' => trans('plugins/car-rentals::booking.booking_information'),
                'content' => view('plugins/car-rentals::bookings.information', ['booking' => $booking])->render(),
                'attributes' => [
                    'style' => 'margin-top: 0',
                ],
            ],
        ];

        if ($canViewCasefile && $tripTimelineContent !== '') {
            $metaBoxes['trip_timeline'] = [
                'title' => trans('plugins/car-rentals::disputes.trip_timeline_title'),
                'content' => $tripTimelineContent,
                'attributes' => [
                    'style' => 'margin-top: 0',
                ],
            ];
        }

        $metaBoxes['trip_messaging'] = [
            'title' => trans('plugins/car-rentals::disputes.trip_messaging_title'),
            'content' => $booking?->id ? view('plugins/car-rentals::partials.trip-messaging', [
                'booking' => $booking,
                'fetchUrl' => route('car-rentals.bookings.messages.index', $booking->id),
                'storeUrl' => route('car-rentals.bookings.messages.store', $booking->id),
                'deescalateUrl' => route('car-rentals.bookings.messages.deescalate', $booking->id),
                'compactAdmin' => true,
            ])->render() : '',
            'attributes' => [
                'style' => 'margin-top: 0',
            ],
        ];

        $this
            ->model(Booking::class)
            ->setValidatorClass(UpdateBookingRequest::class)
            ->withCustomFields()
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(BookingStatusEnum::labels()))
            ->setBreakFieldPoint('status')
            ->addMetaBoxes($metaBoxes);
    }
}
