<div class="sidebar-canvas-wrapper perfect-scrollbar button-bg-2">
    <div class="sidebar-canvas-container">
        <div class="sidebar-canvas-head">
            <div class="sidebar-canvas-logo">
                {!! Theme::partial('logo') !!}
            </div>
            <div class="sidebar-canvas-lang">
                <a class="close-canvas" href="#" title="{{ __('Close') }}"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a>
            </div>
        </div>

        <div class="sidebar-canvas-content">
            {!! dynamic_sidebar('off_canvas_sidebar') !!}
        </div>
    </div>
</div>
