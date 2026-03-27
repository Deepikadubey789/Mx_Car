@if (empty($widgetSetting) || $widgetSetting->status == 1)
    @php
        /* This handles the colors safely whether your system uses hex codes or bootstrap classes */
        $colorClass = !str_contains($widget->color, '#') ? 'bg-' . str_replace('bg-', '', $widget->color) : '';
        $colorStyle = str_contains($widget->color, '#') ? 'background-color: ' . $widget->color . ' !important;' : '';
    @endphp

    <div class="{{ $widget->column ?: 'col-12 col-sm-6 col-md-3' }} mb-4">
        <a href="{{ $widget->route ?: 'javascript:void(0)' }}" style="text-decoration: none;">
            <div class="card shadow-sm border-0 {{ $colorClass }}" style="border-radius: 1rem; transition: transform 0.2s; {{ $colorStyle }}">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-8 text-start">
                            <p class="text-sm mb-1 text-uppercase font-weight-bold" style="color: rgba(255,255,255,0.8); letter-spacing: 0.5px;">
                                {{ $widget->title }}
                            </p>
                            <h5 class="font-weight-bolder mb-0 text-white" style="font-size: 1.75rem;">
                                {{ is_int($widget->statsTotal) ? number_format($widget->statsTotal) : $widget->statsTotal }}
                            </h5>
                        </div>
                        <div class="col-4 text-end">
                            <div class="d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255,255,255,0.25); border-radius: 0.75rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                {{-- Use the core component to safely render SVGs or font icons --}}
                                @if (str_contains($widget->icon, '<'))
                                    {!! $widget->icon !!}
                                @else
                                    <x-core::icon :name="$widget->icon" style="width: 24px; height: 24px; color: #ffffff;" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
@endif