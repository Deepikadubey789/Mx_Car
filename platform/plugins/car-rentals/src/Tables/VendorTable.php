<?php

namespace Botble\CarRentals\Tables;

use Botble\Base\Facades\Html;
use Botble\CarRentals\Models\Customer;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\ViewAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EmailColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Botble\CarRentals\Models\VendorQualityScore;

class VendorTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Customer::class)
            ->addActions([
                ViewAction::make()
                    ->route('car-rentals.vendors.view')
                    ->permission('car-rentals.vendors.view'),
                EditAction::make()->route('car-rentals.vendors.edit'),
                DeleteAction::make()->route('car-rentals.vendors.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make('name')
                    ->route('car-rentals.vendors.edit')
                    ->renderUsing(function (NameColumn $column) {
                        $item = $column->getItem();

                        $name = Html::link(route('car-rentals.vendors.edit', $item->id), $item->name);

                        if ($item->is_verified) {
                            $name .= ' <span class="badge bg-blue text-blue-fg ms-1">' . trans('plugins/car-rentals::car-rentals.vendor.verified') . '</span>';
                        }

                        return $name;
                    }),
                EmailColumn::make(),
                Column::make('phone'),
                FormattedColumn::make('total_cars')
                    ->title(trans('plugins/car-rentals::car-rentals.vendor.total_cars'))
                    ->width(100)
                    ->orderable(false)
                    ->searchable(false)
                    ->alignCenter()
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return $item->cars()->count();
                    }),
                FormattedColumn::make('total_bookings')
                    ->title(trans('plugins/car-rentals::car-rentals.vendor.total_bookings'))
                    ->width(100)
                    ->alignCenter()
                    ->orderable(false)
                    ->searchable(false)
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return $item->vendorBookings()->count();
                    }),
                FormattedColumn::make('quality_score')
                    ->title('Quality Score')
                    ->width(100)
                    ->orderable(false)
                    ->searchable(false)
                    ->alignCenter()
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        $score = \Botble\CarRentals\Models\VendorQualityScore::where('vendor_id', $item->id)->first();
                        if (!$score) return '<span class="text-muted">-</span>';
                        $color = match($score->badge_tier) {
                            'all_star'    => 'warning',
                            'top_host'    => 'success',
                            'rising_star' => 'info',
                            default       => 'secondary',
                        };
                        $label = number_format($score->total_score, 0) . '/100';
                        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
                    }),
                FormattedColumn::make('badge_tier')
                    ->title('Badge')
                    ->width(130)
                    ->orderable(false)
                    ->searchable(false)
                    ->alignCenter()
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        $score = \Botble\CarRentals\Models\VendorQualityScore::where('vendor_id', $item->id)->first();
                        if (!$score || $score->badge_tier === 'none') return '<span class="text-muted">None</span>';
                        return match($score->badge_tier) {
                            'all_star'    => '<span class="badge bg-warning text-dark">⭐ All-Star</span>',
                            'top_host'    => '<span class="badge bg-success">🏆 Top Host</span>',
                            'rising_star' => '<span class="badge bg-info">🌟 Rising Star</span>',
                            default       => '<span class="text-muted">None</span>',
                        };
                    }),
                FormattedColumn::make('accept_complete')
                    ->title('Accept / Complete')
                    ->width(140)
                    ->orderable(false)
                    ->searchable(false)
                    ->alignCenter()
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        $score = \Botble\CarRentals\Models\VendorQualityScore::where('vendor_id', $item->id)->first();
                        if (!$score) return '<span class="text-muted">-</span>';
                        return $score->acceptance_rate . '% / ' . $score->completion_rate . '%';
                    }),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('car-rentals.vendors.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query): void {
                $query->where('is_vendor', true)
                    ->select([
                        'id',
                        'name',
                        'avatar',
                        'email',
                        'phone',
                        'status',
                        'created_at',
                        'is_verified',
                        'is_vendor',
                    ]);
            });
    }
}
