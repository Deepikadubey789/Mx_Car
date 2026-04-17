@extends('core/base::layouts.master')

@section('content')
<div class="container-xl">
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Edit WhatsApp Template</h2>
                    <div class="page-pretitle">{{ $template->name }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('car-rentals.whatsapp.templates.index') }}" class="btn btn-outline-secondary">Back to Templates</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="container-xl">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('car-rentals.whatsapp.templates.update', $template) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Template Name</label>
                                <input type="text" class="form-control" value="{{ $template->name }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Type</label>
                                <input type="text" class="form-control" value="{{ $template->event_type }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Label</label>
                            <input
                                type="text"
                                name="label"
                                class="form-control @error('label') is-invalid @enderror"
                                value="{{ old('label', $template->label) }}"
                                required
                            >
                            @error('label')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea
                                name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="2"
                            >{{ old('description', $template->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Template Content</label>
                            <textarea
                                name="template_content"
                                class="form-control @error('template_content') is-invalid @enderror"
                                rows="10"
                                required
                            >{{ old('template_content', $template->template_content) }}</textarea>
                            <small class="form-text text-muted">Use placeholders in double braces, for example: <code>@{{booking_reference}}</code></small>
                            @error('template_content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Placeholders</label>
                            <input
                                type="text"
                                name="placeholders"
                                class="form-control @error('placeholders') is-invalid @enderror"
                                value="{{ old('placeholders', $placeholdersText) }}"
                                placeholder="booking_reference, pickup_date, total_amount"
                            >
                            <small class="form-text text-muted">Comma-separated placeholder names without braces.</small>
                            @error('placeholders')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="hidden" name="is_active" value="0">
                            <label class="form-check">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    class="form-check-input"
                                    @checked((bool) old('is_active', $template->is_active))
                                >
                                <span class="form-check-label">Template is active</span>
                            </label>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">Save Template</button>
                            <a href="{{ route('car-rentals.whatsapp.templates.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
