<tr>
    <td>
        <strong>{{ \Carbon\Carbon::parse($recommendation->recommendation_date)->format('M d, Y') }}</strong>
    </td>
    <td>${{ number_format($recommendation->local_baseline_price ?? 150, 2) }}</td>
    <td>
        <strong class="text-success">${{ number_format($recommendation->recommended_value, 2) }}</strong>
        <br>
        <small class="text-muted">
            @if ($recommendation->recommended_value > ($recommendation->local_baseline_price ?? 150))
                ↑ {{ number_format(((($recommendation->recommended_value - ($recommendation->local_baseline_price ?? 150)) / ($recommendation->local_baseline_price ?? 150)) * 100), 1) }}%
            @else
                ↓ {{ number_format(((($recommendation->local_baseline_price ?? 150) - $recommendation->recommended_value) / ($recommendation->local_baseline_price ?? 150)) * 100), 1) }}%
            @endif
        </small>
    </td>
    <td>
        <span class="badge bg-{{ $recommendation->confidence_label['color'] }}" title="{{ $recommendation->confidence_label['description'] }}">
            {{ intval($recommendation->confidence_score * 100) }}%
        </span>
        <br>
        <small class="text-muted">{{ $recommendation->confidence_label['label'] }}</small>
    </td>
    <td>
        @if ($recommendation->estimated_revenue_impact)
            <strong class="text-success">${{ number_format($recommendation->estimated_revenue_impact, 2) }}</strong>
            <br>
            <small class="text-muted">per booking</small>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td>
        @include('plugins/car-rentals::themes.vendor-dashboard.pricing.recommendation-actions', ['recommendation' => $recommendation])
    </td>
</tr>
