@extends('core/base::forms.form-tabs')

@section('form_end')
    {!! apply_filters('car_rentals_customer_form_end', null, $form) !!}
@endsection
