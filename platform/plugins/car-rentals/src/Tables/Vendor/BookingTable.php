<?php

namespace Botble\CarRentals\Tables\Vendor;

use Botble\CarRentals\Tables\BookingTable as BaseBookingTable;
use Botble\CarRentals\Tables\Traits\ForVendor;
use Botble\Table\Actions\ViewAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;

class BookingTable extends BaseBookingTable
{
    use ForVendor;

    public function setup(): void
    {
        parent::setup();

        $this
            ->removeAllActions()
            ->addActions([
                ViewAction::make()->route('car-rentals.vendor.bookings.show'),
            ])
            ->removeColumns()
            ->addColumns([
                Column::make('id'),
                FormattedColumn::make('customer_name')
                    ->label(trans('plugins/car-rentals::booking.customer'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        // Eager load the customer and their reviews to prevent slow database queries
                        $item->loadMissing(['customer.receivedReviews']);

                        $name = $item->customer ? $item->customer->name : ($item->customer_name ?: '-');

                        // If the customer has an account and has reviews, show the stars!
                        if ($item->customer && $item->customer->total_reviews > 0) {
                            $rating = number_format($item->customer->average_rating, 1);
                            $reviewsCount = $item->customer->total_reviews;
                            
                            // Build a neat little star badge using Botble's native Tabler icons
                            $starsHtml = '<div class="mt-1 d-flex align-items-center text-warning" style="font-size: 0.85rem;">
                                            <i class="ti ti-star-filled me-1"></i> ' . $rating . ' 
                                            <span class="text-muted ms-1">(' . $reviewsCount . ')</span>
                                          </div>';
                                          
                            return '<strong>' . $name . '</strong>' . $starsHtml;
                        }

                        return '<strong>' . $name . '</strong>';
                    }),

                    
                FormattedColumn::make('amount')
                    ->label(trans('plugins/car-rentals::booking.amount'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return format_price($item->amount, $item->currency_id);
                    }),
                FormattedColumn::make('rental_period')
                    ->orderable(false)
                    ->searchable(false)
                    ->label(trans('plugins/car-rentals::booking.rental_period'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        $item->loadMissing('car');

                        if (! $car = $item->car) {
                            return '-';
                        }

                        return sprintf('%s - %s', $car->rental_start_date_formatted, $car->rental_end_date_formatted);
                    }),
                CreatedAtColumn::make(),
                FormattedColumn::make('payment_method')
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        if (! is_plugin_active('payment')) {
                            return '-';
                        }

                        $item->loadMissing('payment');

                        return $item->payment ? $item->payment->payment_channel->label() : '-';
                    })
                    ->title(trans('plugins/car-rentals::booking.payment_method')),
                FormattedColumn::make('payment_id')
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        if (! is_plugin_active('payment')) {
                            return '-';
                        }

                        $item->loadMissing('payment');

                        return $item->payment ? $item->payment->status->toHtml() : '-';
                    })
                    ->title(trans('plugins/car-rentals::booking.payment_status')),
                StatusColumn::make(),
            ])
            ->queryUsing(function (Builder $query): void {
                $query
                    ->select([
                        'id',
                        'customer_name',
                        'customer_id',
                        'currency_id',
                        'payment_id',
                        'amount',
                        'status',
                        'created_at',
                    ])
                    ->with('car', 'customer', 'currency', 'payment')
                    ->where('vendor_id', auth('customer')->id());
            });
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function getBulkChanges(): array
    {
        return [];
    }

    public function hasBulkActions(): bool
    {
        return false;
    }
}
