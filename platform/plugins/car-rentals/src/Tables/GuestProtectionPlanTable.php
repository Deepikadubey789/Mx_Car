<?php

namespace Botble\CarRentals\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\CarRentals\Models\GuestProtectionPlan;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class GuestProtectionPlanTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(GuestProtectionPlan::class)
            ->addActions([
                EditAction::make()->route('car-rentals.guest-protection-plans.edit'),
                DeleteAction::make()->route('car-rentals.guest-protection-plans.destroy'),
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
                'daily_fee',
                'deductible_amount',
                'created_at',
                'status',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            NameColumn::make()->route('car-rentals.guest-protection-plans.edit'),
            Column::make('daily_fee')->title('Daily Fee')->alignCenter(),
            Column::make('deductible_amount')->title('Deductible')->alignCenter(),
            CreatedAtColumn::make(),
            StatusColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('car-rentals.guest-protection-plans.create'), 'car-rentals.cars.edit');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('car-rentals.cars.edit'),
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
                'type' => 'customSelect',
                'choices' => \Botble\Base\Enums\BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', \Botble\Base\Enums\BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}