@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
<div class="card shadow-sm border-0" style="border-radius: 1rem;">
    <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold">
            <i class="ti ti-calendar-stats text-primary me-2"></i>{{ __('Fleet Schedule') }}
        </h5>
    </div>
    <div class="card-body">
        <div id="fleet-timeline-calendar"></div>
    </div>
</div>

<style>
    #fleet-timeline-calendar {
        min-height: 600px;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 4px;
    }
    .fc-event-title {
        font-weight: 600;
        font-size: 0.85em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('fleet-timeline-calendar');
        
        // Switched to standard open-source views (dayGridMonth, timeGridWeek, listWeek)
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: '{{ route("car-rentals.vendor.fleet-calendar.events") }}',
            height: 'auto',
            nowIndicator: true,
            displayEventTime: false, // Hides 12:00a text on multi-day events
            eventClick: function(info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            }
        });

        calendar.render();
    });
</script>
@stop