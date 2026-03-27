@php
    $formId = $formId ?? 'cars-filter-form';
@endphp

{!! Form::open(['url' => route('public.ajax.cars'), 'method' => 'GET', 'id' => $formId, 'class' => 'sidebar-filter-mobile__content']) !!}
    <input type="hidden" name="page" value="{{ $cars->currentPage() ?: 1 }}" data-value="{{ $cars->currentPage() ?: 1 }}" />
    <input type="hidden" name="per_page" value="{{ BaseHelper::stringify(request()->query('per_page', $cars->perPage())) }}" />
    <input type="hidden" name="layout" value="{{ $layout }}" />
    <input type="hidden" name="layout_col" value="{{ $col }}" />
    <input type="hidden" name="filter" value="{{ $enableFilter }}" />
    <input type="hidden" name="sort_by" value="{{ BaseHelper::stringify(request()->query('sort_by')) }}" />
    <input type="hidden" name="start_date" value="{{ BaseHelper::stringify(request()->query('start_date')) }}" />
    <input type="hidden" name="end_date" value="{{ BaseHelper::stringify(request()->query('end_date')) }}" />

    @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.filter-content'), ['formId' => $formId])
{!! Form::close() !!}
