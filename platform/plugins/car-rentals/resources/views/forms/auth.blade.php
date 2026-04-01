@php
    $icon = Arr::get($formOptions, 'icon');
    $heading = Arr::get($formOptions, 'heading');
    $description = Arr::get($formOptions, 'description');
@endphp

@if (Arr::get($formOptions, 'has_wrapper', 'yes') === 'yes')
<div class="auth-split-wrapper">
    <div class="auth-split-container">
        <div class="auth-left-panel">
            <div class="auth-left-overlay"></div>
            <div class="auth-left-content">
                <div class="auth-brand-name">MXCar</div>
                <p class="auth-brand-desc">Your trusted vehicle rental partner. Experience premium car rentals with unmatched service.</p>
                <ul class="auth-features">
                    <li>Secure &amp; Trusted Platform</li>
                <li>24/7 Customer Support</li>
                    <li>Premium Vehicle Fleet</li>
                    <li>Nationwide Coverage</li>
                </ul>
            </div>
        </div>
        <div class="auth-right-panel">
@endif
            <div class="card border-0 auth-card">
                @if ($icon || $heading || $description)
                    <div class="card-header border-0 p-4 pb-0 bg-transparent text-center">
                        @if ($icon)
                            <div class="auth-icon-wrap mx-auto mb-3">
                                <x-core::icon :name="$icon" class="text-white" />
                            </div>
                        @endif
                        @if ($heading)
                            <h3 class="fs-4 mb-1">{{ $heading }}</h3>
                        @endif
                        @if ($description)
                            <p class="text-muted small">{{ $description }}</p>
                        @endif
                    </div>
                @endif
                <div class="card-body p-4 pt-3">
                    @if ($showStart)
                        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
                    @endif
                    @if (session()->has('status'))
                        <div role="alert" class="alert alert-success">{{ session('status') }}</div>
                    @elseif (session()->has('auth_error_message'))
                        <div role="alert" class="alert alert-danger">{{ session('auth_error_message') }}</div>
                    @elseif (session()->has('auth_success_message'))
                        <div role="alert" class="alert alert-success">{{ session('auth_success_message') }}</div>
                    @elseif (session()->has('auth_warning_message'))
                        <div role="alert" class="alert alert-warning">{{ session('auth_warning_message') }}</div>
                    @endif
                    @if ($showFields)
                        {{ $form->getOpenWrapperFormColumns() }}
                        @foreach ($fields as $field)
                            @continue(in_array($field->getName(), $exclude))
                            {!! $field->render() !!}
                        @endforeach
                        {{ $form->getCloseWrapperFormColumns() }}
                    @endif
                    @if ($showEnd)
                        {!! Form::close() !!}
                    @endif
                    @if ($form->getValidatorClass())
                        @push('footer')
                            {!! $form->renderValidatorJs() !!}
                        @endpush
                    @endif
                </div>
            </div>
@if (Arr::get($formOptions, 'has_wrapper', 'yes') === 'yes')
        </div>
    </div>
</div>
@endif
