@once
    <div class="nav-item d-none d-md-flex me-2">
        <a
            class="px-0 nav-link"
            data-bs-toggle="offcanvas"
            href="#notification-sidebar"
            role="button"
            aria-controls="notification-sidebar"
        >
            <x-core::icon name="ti ti-bell" />
            <span
                class="badge bg-yellow badge-pill notification-count"
                style="color: #fff !important; @if(! $countNotificationUnread) display: none; @endif"
            >{{ number_format($countNotificationUnread) }}</span>
        </a>
    </div>
@endonce
