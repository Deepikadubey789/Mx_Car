<?php

namespace Botble\CarRentals\Tables;

use Botble\CarRentals\Models\CarType;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class CarTypeTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CarType::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('car-rentals.car-types.create'))
            ->addActions([
                EditAction::make()->route('car-rentals.car-types.edit'),
                DeleteAction::make()->route('car-rentals.car-types.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('car-rentals.car-types.edit'),
                ImageColumn::make('image'),
                Column::make('icon')
                    ->title(trans('plugins/car-rentals::car-rentals.icon'))
                    ->width(100)
                    ->alignCenter(),
                Column::make('order')
                    ->title(trans('core/base::tables.order'))
                    ->width(100)
                    ->alignCenter(),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('car-rentals.car-types.destroy'),
            ])
            ->queryUsing(function (Builder $query): void {
                $query->select([
                    'id',
                    'name',
                    'image',
                    'icon',
                    'order',
                    'status',
                    'created_at',
                ]);
            });
    }
}
