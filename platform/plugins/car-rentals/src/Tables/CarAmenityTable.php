<?php

namespace Botble\CarRentals\Tables;

use Botble\CarRentals\Models\CarAmenity;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class CarAmenityTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CarAmenity::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('car-rentals.car-amenities.create'))
            ->addActions([
                EditAction::make()->route('car-rentals.car-amenities.edit'),
                DeleteAction::make()->route('car-rentals.car-amenities.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make('name')->route('car-rentals.car-amenities.edit'),
                FormattedColumn::make('category_id')
                    ->title(trans('plugins/car-rentals::car-amenity-category.category'))
                    ->width(150)
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return $item->category ? $item->category->name : '&mdash;';
                    }),
                Column::make('order')
                    ->title(trans('core/base::tables.order'))
                    ->width(100)
                    ->alignCenter(),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('car-rentals.car-amenities.destroy'),
            ])
            ->queryUsing(function (Builder $query): void {
                $query->select([
                    'id',
                    'name',
                    'category_id',
                    'order',
                    'status',
                    'created_at',
                ])->with(['category']);
            });
    }
}
