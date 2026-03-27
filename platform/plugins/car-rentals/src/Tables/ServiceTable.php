<?php

namespace Botble\CarRentals\Tables;

use Botble\CarRentals\Models\Service;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class ServiceTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Service::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('car-rentals.services.create'))
            ->addActions([
                EditAction::make()->route('car-rentals.services.edit'),
                DeleteAction::make()->route('car-rentals.services.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('car-rentals.services.edit'),
                FormattedColumn::make('price')
                    ->title(trans('plugins/car-rentals::car-rentals.service.forms.price'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $service = $column->getItem();

                        return format_price_in_default_currency($service->price, $service->currency_id);
                    }),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('car-rentals.services.destroy'),
            ])
            ->queryUsing(function (Builder $query): void {
                $query->select([
                    'id',
                    'name',
                    'price',
                    'currency_id',
                    'status',
                    'created_at',
                ]);
            });
    }
}
