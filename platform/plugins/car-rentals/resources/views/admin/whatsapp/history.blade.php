@extends('core/base::layouts.master')

@section('content')
<div class="container-xl">
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">WhatsApp Message History</h2>
                    <div class="page-pretitle">Customer: {{ $customer->name }} ({{ $customer->whatsapp }})</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('car-rentals.whatsapp.send') }}" class="btn btn-primary">
                        Send Message
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Event Type</th>
                                        <th>Template</th>
                                        <th>Booking</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sentMessages as $message)
                                        <tr>
                                            <td>
                                                <small>{{ $message->created_at->format('M d, Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-blue-lt">{{ ucfirst(str_replace('_', ' ', $message->event_type)) }}</span>
                                            </td>
                                            <td>
                                                @if ($message->template_name)
                                                    <span class="badge bg-green-lt">{{ $message->template_name }}</span>
                                                @else
                                                    <span class="text-muted">Custom</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($message->booking)
                                                    <a href="{{ route('car-rentals.bookings.edit', $message->booking->id) }}">
                                                        {{ $message->booking->booking_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($message->status === 'accepted')
                                                    <span class="badge bg-primary">Accepted</span>
                                                @elseif ($message->status === 'sent')
                                                    <span class="badge bg-azure">Sent</span>
                                                @elseif ($message->status === 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif ($message->status === 'read')
                                                    <span class="badge bg-green">Read</span>
                                                @elseif ($message->status === 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-yellow">Pending</span>
                                                @endif
                                                @if ($message->provider_message_id)
                                                    <div><small class="text-muted">ID: {{ $message->provider_message_id }}</small></div>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-ghost-primary" data-bs-toggle="modal" data-bs-target="#messageModal{{ $message->id }}">
                                                    Preview
                                                </button>

                                                <!-- Message Preview Modal -->
                                                <div class="modal fade" id="messageModal{{ $message->id }}" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel{{ $message->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="messageModalLabel{{ $message->id }}">Message Content</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p style="white-space: pre-wrap; word-break: break-word;">{{ $message->message_content }}</p>
                                                            </div>
                                                            @if ($message->status === 'failed' && $message->error_message)
                                                                <div class="alert alert-danger m-3 mb-0">
                                                                    <strong>Error:</strong> {{ $message->error_message }}
                                                                </div>
                                                            @endif
                                                            @if ($message->meta_response)
                                                                <div class="modal-body border-top">
                                                                    <small class="text-muted">
                                                                        <strong>Meta Response:</strong><br>
                                                                        <code style="font-size: 0.8em;">{{ json_encode($message->meta_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code>
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                No messages sent to this customer yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($sentMessages->hasPages())
                            <div class="card-footer d-flex align-items-center">
                                {{ $sentMessages->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
