@if ($recommendation->status === 'pending')
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="ti ti-dots-vertical me-1"></i> Actions
        </button>
        <ul class="dropdown-menu">
            <!-- Apply -->
            <li>
                <form action="{{ route('car-rentals.vendor.demand-pricing.recommendations.apply', $recommendation->id) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Apply this recommendation for ${{ number_format($recommendation->recommended_value, 2) }}?')">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="ti ti-check me-2 text-success"></i> Apply
                    </button>
                </form>
            </li>

            <!-- Adjust -->
            <li>
                <a href="javascript:void(0)" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $recommendation->id }}">
                    <i class="ti ti-edit me-2 text-warning"></i> Adjust & Apply
                </a>
            </li>

            <!-- Dismiss -->
            <li>
                <a href="javascript:void(0)" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#dismissModal{{ $recommendation->id }}">
                    <i class="ti ti-x me-2"></i> Dismiss
                </a>
            </li>
        </ul>
    </div>

    <!-- Adjust Modal -->
    <div class="modal fade" id="adjustModal{{ $recommendation->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('car-rentals.vendor.demand-pricing.recommendations.adjust', $recommendation->id) }}"
                      method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Adjust the recommended price before applying (±10% allowed).
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Original Recommendation</label>
                            <input type="text" class="form-control" disabled value="${{ number_format($recommendation->recommended_value, 2) }}">
                            <small class="text-muted d-block mt-1">
                                Range: ${{ number_format($recommendation->recommended_value * 0.90, 2) }} to ${{ number_format($recommendation->recommended_value * 1.10, 2) }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Your Price</label>
                            <input type="number"
                                   name="adjusted_price"
                                   class="form-control"
                                   step="0.01"
                                   min="{{ $recommendation->recommended_value * 0.90 }}"
                                   max="{{ $recommendation->recommended_value * 1.10 }}"
                                   value="{{ $recommendation->recommended_value }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Why This Price? (Optional)</label>
                            <textarea name="adjustment_notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="e.g., Based on my experience or local knowledge..."
                                      maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply Adjusted Price</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dismiss Modal -->
    <div class="modal fade" id="dismissModal{{ $recommendation->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dismiss Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('car-rentals.vendor.demand-pricing.recommendations.dismiss', $recommendation->id) }}"
                      method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Help us improve by telling us why you're rejecting this recommendation.
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Reason (Required)</label>
                            <select name="rejected_reason" class="form-select" required>
                                <option value="">-- Select a reason --</option>
                                <option value="too_high">Price is too high</option>
                                <option value="too_low">Price is too low</option>
                                <option value="inventory_issue">Vehicle inventory issue</option>
                                <option value="not_applicable">Not applicable to my car</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Comments (Optional)</label>
                            <textarea name="vendor_notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Tell us more..."
                                      maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Dismiss</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@elseif ($recommendation->status === 'applied')
    <span class="badge bg-success">
        <i class="ti ti-check me-1"></i>
        Applied
        @if ($recommendation->adjustment_applied)
            <br>
            <small>(Adjusted {{ $recommendation->adjustment_applied > 0 ? '+' : '' }}${{ number_format($recommendation->adjustment_applied, 2) }})</small>
        @endif
    </span>
@else
    <span class="badge bg-secondary">
        <i class="ti ti-x me-1"></i>
        Dismissed
    </span>
@endif
