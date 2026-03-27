<x-core::modal.action
    id="vendor-control-modal"
    :type="$isActivated ? 'danger' : 'info'"
    :title="$isActivated ? trans('plugins/car-rentals::vendor.control.block') : trans('plugins/car-rentals::vendor.control.unblock')"
    :form-action="$isActivated ? route('car-rentals.vendors.block', $model) : route('car-rentals.vendors.unblock', $model)"
    :form-attrs="['id' => 'vendor-control-form']"
>
    @if($isActivated)
        {{ trans('plugins/car-rentals::vendor.control.block_confirmation') }}

        <textarea
            name="reason"
            class="form-control mt-3"
            placeholder="{{ trans('plugins/car-rentals::vendor.control.block_reason') }}"
        ></textarea>
    @else
        {{ trans('plugins/car-rentals::vendor.control.unblock_confirmation') }}
    @endif

    <x-slot:footer>
        <div class="w-100">
            <div class="row">
                <div class="col">
                    <x-core::button type="submit" :color="$isActivated ? 'danger' : 'info'" class="w-100" form="vendor-control-form">
                        {{ trans('core/base::tables.submit') }}
                    </x-core::button>
                </div>
                <div class="col">
                    <x-core::button type="button" class="w-100" data-bs-dismiss="modal">
                        {{ trans('core/base::base.close') }}
                    </x-core::button>
                </div>
            </div>
        </div>
    </x-slot:footer>
</x-core::modal.action>
