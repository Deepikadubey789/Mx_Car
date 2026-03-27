<?php

namespace Botble\CarRentals\Tables;

use Botble\CarRentals\Models\Customer;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\Actions\ViewAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EmailColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class CustomerTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Customer::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('car-rentals.customers.create'))
            ->addActions([
                ViewAction::make()
                    ->route('car-rentals.customers.view')
                    ->permission('car-rentals.customers.edit'),
                EditAction::make()->route('car-rentals.customers.edit'),
                DeleteAction::make()->route('car-rentals.customers.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make('name')->route('car-rentals.customers.edit'),
                EmailColumn::make(),
                FormattedColumn::make('phone')->withEmptyState(),
                FormattedColumn::make('is_vendor')
                    ->title(trans('plugins/car-rentals::car-rentals.customer.is_vendor'))
                    ->width(100)
                    ->alignCenter()
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();
                        if ($item->is_vendor) {
                            return '<span class="badge bg-indigo text-indigo-fg">' . trans('plugins/car-rentals::car-rentals.customer.is_vendor') . '</span>';
                        }

                        return '<span class="badge">' . trans('plugins/car-rentals::car-rentals.customer.not_vendor') . '</span>';
                    }),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('car-rentals.customers.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query): void {
                $query->select([
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
