@extends('core/base::layouts.master')

@section('content')
<div class="container-xl">
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">WhatsApp Templates</h2>
                    <div class="page-pretitle">Edit app message templates from UI</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('car-rentals.whatsapp.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="alert alert-warning">
                <strong>Note:</strong> Template approval here is local to this app only.
                You must still approve templates in Meta WhatsApp Manager for Cloud API delivery.
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Event Type</th>
                                <th>Label</th>
                                <th>Content Preview</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templates as $template)
                                <tr>
                                    <td><strong>{{ $template->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-blue-lt">{{ str_replace('_', ' ', $template->event_type) }}</span>
                                    </td>
                                    <td>{{ $template->label }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($template->template_content, 100) }}</td>
                                    <td>
                                        @php
                                            $approvalStatus = data_get($template->metadata, 'approval_status');
                                        @endphp
                                        @if ($template->is_active && $approvalStatus === 'approved')
                                            <span class="badge bg-success">Locally Approved</span>
                                        @elseif ($template->is_active)
                                            <span class="badge bg-azure">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $template->updated_at?->format('M d, Y H:i') }}</small></td>
                                    <td class="text-end">
                                        @if (!$template->is_active || $approvalStatus !== 'approved')
                                            <form action="{{ route('car-rentals.whatsapp.templates.approve', $template) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Mark as Locally Approved</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('car-rentals.whatsapp.templates.edit', $template) }}" class="btn btn-sm btn-primary">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No templates found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($templates->hasPages())
                    <div class="card-footer d-flex align-items-center">
                        {{ $templates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
