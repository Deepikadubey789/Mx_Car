<?php

namespace Botble\CarRentals\Forms;

use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Http\Requests\UpdateBookingRequest;
use Botble\CarRentals\Models\Booking;

class BookingForm extends FormAbstract
{
    public function setup(): void
    {
        /** @var Booking $booking */
        $booking = $this->getModel();

        $this
            ->model(Booking::class)
            ->setValidatorClass(UpdateBookingRequest::class)
            ->withCustomFields()
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(BookingStatusEnum::labels()))
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins/car-rentals::booking.booking_information'),
                    'content' => view('plugins/car-rentals::bookings.information', ['booking' => $booking])->render(),
                    'attributes' => [
                        'style' => 'margin-top: 0',
                    ],
                ],
                'trip_messaging' => [
                    'title' => 'Trip Messaging',
                    'content' => $booking?->id ? view('plugins/car-rentals::partials.trip-messaging', [
                        'booking'      => $booking,
                        'fetchUrl'     => route('car-rentals.bookings.messages.index', $booking->id),
                        'storeUrl'     => route('car-rentals.bookings.messages.store', $booking->id),
                        'deescalateUrl' => route('car-rentals.bookings.messages.deescalate', $booking->id),
                    ])->render() : '',
                    'attributes' => [
                        'style' => 'margin-top: 0',
                    ],
                ],
            ]);
    }
}
