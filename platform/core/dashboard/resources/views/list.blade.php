@extends(BaseHelper::getAdminMasterLayoutTemplate())

@push('header-action')
    @if (count($widgets) > 0 && Auth::user()->isSuperUser())
        <x-core::button
            color="primary"
            :outlined="true"
            class="manage-widget"
            data-bs-toggle="modal"
            data-bs-target="#widgets-management-modal"
            icon="ti ti-layout-dashboard"
        >
            {{ trans('core/dashboard::dashboard.manage_widgets') }}
        </x-core::button>
    @endif
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @if (config('core.base.general.enable_system_updater') && Auth::user()->isSuperUser())
                <v-check-for-updates
                    check-update-url="{{ route('system.check-update') }}"
                    v-slot="{ hasNewVersion, message }"
                    v-cloak
                >
                    <x-core::alert
                        v-if="hasNewVersion"
                        type="warning"
                    >
                        @{{ message }}, please go to <a
                            href="{{ route('system.updater') }}"
                            class="fw-bold"
                        >System Updater</a> to upgrade to the latest version!
                    </x-core::alert>
                </v-check-for-updates>
            @endif
        </div>

        <div class="col-12">
            {!! apply_filters(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, null) !!}
        </div>

        <div class="col-12">
            <div class="row row-cards">
                @foreach ($statWidgets as $widget)
                    {!! $widget !!}
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-4">
    <div class="col-lg-5 mb-lg-0 mb-4">
        <div class="card z-index-2">
            <div class="card-body p-3">
                <div class="bg-gradient-dark-chart border-radius-lg py-3 pe-1 mb-3" style="background: linear-gradient(310deg, #141727 0%, #3a416f 100%); border-radius: 1rem;">
                    <div class="chart">
                        <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                    </div>
                </div>
                <h6 class="ms-2 mt-4 mb-0">Active Users</h6>
                <p class="text-sm ms-2"> (<span class="font-weight-bold">+23%</span>) than last week </p>
                <div class="container border-radius-lg">
                    <div class="row">
                        <div class="col-3 py-3 ps-0">
                            <p class="text-xs mt-1 mb-0 font-weight-bold opacity-7">Users</p>
                            <h6 class="font-weight-bolder">36K</h6>
                        </div>
                        <div class="col-3 py-3 ps-0">
                            <p class="text-xs mt-1 mb-0 font-weight-bold opacity-7">Clicks</p>
                            <h6 class="font-weight-bolder">2m</h6>
                        </div>
                        <div class="col-3 py-3 ps-0">
                            <p class="text-xs mt-1 mb-0 font-weight-bold opacity-7">Sales</p>
                            <h6 class="font-weight-bolder">435$</h6>
                        </div>
                        <div class="col-3 py-3 ps-0">
                            <p class="text-xs mt-1 mb-0 font-weight-bold opacity-7">Items</p>
                            <h6 class="font-weight-bolder">43</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card z-index-2">
            <div class="card-header pb-0">
                <h6>Sales overview</h6>
                <p class="text-sm">
                    <i class="fa fa-arrow-up text-success"></i>
                    <span class="font-weight-bold">4% more</span> in 2026
                </p>
            </div>
            <div class="card-body p-3">
                <div class="chart">
                    <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="mb-3 col-12">
        {!! apply_filters(DASHBOARD_FILTER_TOP_BLOCKS, null) !!}
    </div>

    <div class="col-12">
        <div
            id="list_widgets"
            class="row row-cards"
            data-bb-toggle="widgets-list"
            data-url="{{ route('dashboard.update_widget_order') }}"
        >
            @foreach ($userWidgets as $widget)
                {!! $widget !!}
            @endforeach
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
 
document.addEventListener("DOMContentLoaded", function() {
    // --- 1. BAR CHART (Active Users) ---
    var ctxBar = document.getElementById("chart-bars");
    if (ctxBar) {
        new Chart(ctxBar, {
            type: "bar",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Sales",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#fff",
                    data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
                    maxBarThickness: 6
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { display: false }, ticks: { display: false } },
                    x: { grid: { display: false }, ticks: { display: false } }
                }
            }
        });
    }

    // --- 2. LINE CHART (Sales Overview) ---
    var ctxLine = document.getElementById("chart-line");
    if (ctxLine) {
        var gradientStroke = ctxLine.getContext("2d").createLinearGradient(0, 230, 0, 50);
        gradientStroke.addColorStop(1, 'rgba(203,12,159,0.2)');
        gradientStroke.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        gradientStroke.addColorStop(0, 'rgba(203,12,159,0)');

        new Chart(ctxLine, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Visitors",
                    tension: 0.4,
                    pointRadius: 0,
                    borderColor: "#cb0c9f",
                    borderWidth: 3,
                    backgroundColor: gradientStroke,
                    fill: true,
                    data: [50, 40, 300, 220, 500, 250, 400, 230, 500]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { borderDash: [5, 5], color: '#c1c4ce5c', drawBorder: false }, ticks: { color: '#b2b9bf', padding: 10 } },
                    x: { grid: { display: false }, ticks: { color: '#b2b9bf', padding: 20 } }
                }
            }
        });
    }
});
  
</script>
@endsection

@push('footer')
    @include('core/dashboard::partials.modals', compact('widgets'))
@endpush
