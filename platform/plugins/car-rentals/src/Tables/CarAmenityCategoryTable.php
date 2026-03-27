<?php

namespace Botble\CarRentals\Tables;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Models\CarAmenityCategory;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CarAmenityCategoryTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CarAmenityCategory::class)
            ->addActions([
                EditAction::make()->route('car-rentals.car-amenity-categories.edit'),
                DeleteAction::make()->route('car-rentals.car-amenity-categories.destroy'),
            ]);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'name',
                'order',
                'status',
                'created_at',
            ])
            ->oldest('order')
            ->oldest('name');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            NameColumn::make()->route('car-rentals.car-amenity-categories.edit'),
            'order' => [
                'title' => trans('core/base::forms.order'),
                'class' => 'text-center',
                'width' => '100px',
            ],
            StatusColumn::make(),
            CreatedAtColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('car-rentals.car-amenity-categories.create'), 'car-rentals.car-amenity-categories.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('car-rentals.car-amenity-categories.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function getFilters(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => ['' => trans('core/base::tables.all')] + BaseStatusEnum::labels(),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
