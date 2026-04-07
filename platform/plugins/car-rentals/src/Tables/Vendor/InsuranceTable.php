<?php

namespace Botble\CarRentals\Tables\Vendor;

use Botble\Base\Facades\BaseHelper;
use Botble\CarRentals\Models\Insurance;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;

class InsuranceTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Insurance::class)
            ->addActions([
                EditAction::make()->route('car-rentals.vendor.insurances.edit'),
                DeleteAction::make()->route('car-rentals.vendor.insurances.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('car-rentals.vendor.insurances.edit'),
                Column::make('price')
                    ->title(__('Price'))
                    ->alignLeft(),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ]);
    }

    public function query(): Builder
    {
        return $this->getModel()
            ->query()
            ->select(['id', 'name', 'price', 'created_at', 'status'])
            ->where('vendor_id', auth('customer')->id());
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('car-rentals.vendor.insurances.create'), 'car-rentals.vendor.insurances.create');
    }
}