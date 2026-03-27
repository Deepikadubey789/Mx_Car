<div class="modal fade" id="create-customer-modal" tabindex="-1" role="dialog" aria-labelledby="create-customer-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-customer-modal-label">{{ trans('plugins/car-rentals::booking.create_new_customer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-error-msg" class="alert alert-danger d-none"></div>

                <div id="create-customer-form">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="modal_customer_name" class="form-label">{{ trans('plugins/car-rentals::booking.customer_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_customer_name" name="name" required placeholder="{{ trans('plugins/car-rentals::booking.customer_name') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_customer_email" class="form-label">{{ trans('plugins/car-rentals::booking.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="modal_customer_email" name="email" required placeholder="{{ trans('plugins/car-rentals::booking.email_placeholder') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_customer_phone" class="form-label">{{ trans('plugins/car-rentals::booking.phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_customer_phone" name="phone" required placeholder="{{ trans('plugins/car-rentals::booking.phone_placeholder') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="create-customer-button">{{ trans('plugins/car-rentals::booking.create_new_customer') }}</button>
            </div>
        </div>
    </div>
</div>
