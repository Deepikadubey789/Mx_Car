@php
    Theme::set('pageName', $category->name);
    Theme::set('breadcrumbs', true);
    Theme::set('breadcrumb_simple', true);
    
    // Safely grab the FAQs for this specific category
    $categoryFaqs = $faqs ?? $category->faqs;
@endphp

<style>
/* Premium FAQ Styles */
.mxcar-faq-clean-section { padding: 80px 0; }
.mxcar-faq-clean-container { max-width: 1000px; margin: 0 auto; }
.mxcar-faq-box {
    background-color: #ffffff !important;
    border: 1px solid #E9ECEF !important;
    border-radius: 12px !important;
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 24px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
}
.mxcar-faq-box:hover {
    border-color: #E9ECEF !important;
    box-shadow: 0 8px 30px rgba(176, 58, 46, 0.05) !important;
}
.mxcar-faq-header { padding: 28px 32px !important; }
a.mxcar-faq-header {
    background-color: #ffffff !important;
    color: #111111 !important;
    text-decoration: none !important;
    padding: 28px 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border-left: 5px solid transparent !important;
    transition: all 0.3s ease;
    min-height: 115px !important;
}
a.mxcar-faq-header:not(.collapsed) {
    background-color: #FAFAFA !important;
    border-left: 5px solid transparent !important;
}
.mxcar-faq-title {
    margin: 0 !important;
    font-size: 1.15rem !important;
    font-weight: 600 !important;
    color: #111111 !important;
    line-height: 1.4 !important;
}
a.mxcar-faq-header:not(.collapsed) .mxcar-faq-title { color: #B03A2E !important; }
.mxcar-faq-icon-wrap {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    background-color: #F4F5F7 !important;
    transition: all 0.3s ease !important;
    flex-shrink: 0;
}
.mxcar-faq-icon-wrap svg { stroke: #111111 !important; }
a.mxcar-faq-header:not(.collapsed) .mxcar-faq-icon-wrap {
    transform: rotate(180deg) !important;
    background-color: rgba(176, 58, 46, 0.1) !important;
}
a.mxcar-faq-header:not(.collapsed) .mxcar-faq-icon-wrap svg { stroke: #B03A2E !important; }
.mxcar-faq-body {
    border-top: 1px solid #F4F5F7 !important;
    padding: 28px 32px !important;
    background-color: #FAFAFA !important;
}
.mxcar-faq-body p,
.mxcar-faq-body p * {
    color: #6C757D !important;
    font-size: 1.05rem !important;
    line-height: 1.7 !important;
    margin: 0 !important;
}
.mxcar-page-title {
    color: #111111 !important;
    font-weight: 800 !important;
    font-size: 3rem !important;
    letter-spacing: -1px;
}
.mxcar-page-desc {
    color: #6C757D !important;
    font-size: 1.1rem !important;
    max-width: 650px !important;
    margin: 0 auto !important;
}

/* Dark Mode */
[data-bs-theme="dark"] .mxcar-faq-clean-section { background-color: transparent !important; }
[data-bs-theme="dark"] .mxcar-faq-box { background-color: #1a1a1a !important; border-color: #2e2e2e !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important; }
[data-bs-theme="dark"] .mxcar-faq-box:hover { border-color: #2e2e2e !important; }
[data-bs-theme="dark"] a.mxcar-faq-header { background-color: #1a1a1a !important; color: #f1f1f1 !important; }
[data-bs-theme="dark"] a.mxcar-faq-header:not(.collapsed) { background-color: #242424 !important; }
[data-bs-theme="dark"] .mxcar-faq-title { color: #f1f1f1 !important; }
[data-bs-theme="dark"] a.mxcar-faq-header:not(.collapsed) .mxcar-faq-title { color: #B03A2E !important; }
[data-bs-theme="dark"] .mxcar-faq-icon-wrap { background-color: #2e2e2e !important; }
[data-bs-theme="dark"] .mxcar-faq-icon-wrap svg { stroke: #f1f1f1 !important; }
[data-bs-theme="dark"] a.mxcar-faq-header:not(.collapsed) .mxcar-faq-icon-wrap { background-color: rgba(176, 58, 46, 0.2) !important; }
[data-bs-theme="dark"] .mxcar-faq-body { border-top: 1px solid #2e2e2e !important; background-color: #242424 !important; }
[data-bs-theme="dark"] .mxcar-faq-body p,
[data-bs-theme="dark"] .mxcar-faq-body p * { color: #adb5bd !important; }
[data-bs-theme="dark"] .mxcar-page-title { color: #f1f1f1 !important; }
[data-bs-theme="dark"] .mxcar-page-desc { color: #adb5bd !important; }
</style>

<div class="mxcar-faq-clean-section">
    <div class="container mxcar-faq-clean-container">
        
        <div class="text-center mb-5 pb-4">
            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 mb-3 fw-bold" style="letter-spacing: 1px;">{{ __('FAQ CATEGORY') }}</span>
            <h1 class="mb-3 mxcar-page-title">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mxcar-page-desc">{{ $category->description }}</p>
            @endif
        </div>

        <div class="row g-4">
            @forelse($categoryFaqs as $faq)
                @php
                    $id = 'faq-item-' . md5($faq->question) . '-' . $faq->id;
                @endphp
                <div class="col-12 col-lg-6">
                    <div class="mxcar-faq-box">
                        <a class="mxcar-faq-header collapsed" data-bs-toggle="collapse" href="#{{ $id }}" role="button" aria-expanded="false" aria-controls="{{ $id }}">
                            <p class="mxcar-faq-title pe-4">{!! BaseHelper::clean($faq->question) !!}</p>
                            <div class="mxcar-faq-icon-wrap">
                                <x-core::icon name="ti ti-chevron-down" class="stroke-dark" />
                            </div>
                        </a>
                        <div id="{{ $id }}" class="collapse">
                            <div class="mxcar-faq-body">
                                <p>{!! BaseHelper::clean($faq->answer) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">{{ __('No questions have been added to this category yet.') }}</h4>
                </div>
            @endforelse
        </div>
        
    </div>
</div>