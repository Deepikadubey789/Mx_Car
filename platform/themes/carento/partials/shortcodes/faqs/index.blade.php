@php
    $signature = md5(json_encode([
        'title' => $shortcode->title ?? '',
        'description' => $shortcode->description ?? '',
        'faq_category_ids' => $shortcode->faq_category_ids ?? '',
        'limit' => $shortcode->limit ?? '',
        'button_secondary_label' => $shortcode->button_secondary_label ?? '',
        'button_secondary_url' => $shortcode->button_secondary_url ?? '',
        'button_primary_label' => $shortcode->button_primary_label ?? '',
        'button_primary_url' => $shortcode->button_primary_url ?? '',
    ]));

    if (! isset($GLOBALS['rendered_faqs_signatures'])) {
        $GLOBALS['rendered_faqs_signatures'] = [];
    }

    if (in_array($signature, $GLOBALS['rendered_faqs_signatures'], true)) {
        return; /* identical FAQ block already rendered on this page */
    }

    $GLOBALS['rendered_faqs_signatures'][] = $signature;
@endphp

<style>
/* Clean Ultra-Light FAQ Redesign (Contact Page Match) */
.mxcar-faq-clean-section {
    position: relative;
    padding: 80px 0;
}
.mxcar-faq-clean-container {
    max-width: 1000px;
    margin: 0 auto;
}
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
.mxcar-faq-header {
    padding: 28px 32px !important;
}
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
a.mxcar-faq-header:not(.collapsed) .mxcar-faq-title {
    color: #B03A2E !important;
}
.mxcar-faq-icon-wrap {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    background-color: #F4F5F7 !important;
    transition: all 0.3s ease !important;
}
.mxcar-faq-icon-wrap svg {
    stroke: #111111 !important;
}
a.mxcar-faq-header:not(.collapsed) .mxcar-faq-icon-wrap {
    transform: rotate(180deg) !important;
    background-color: rgba(176, 58, 46, 0.1) !important;
}
a.mxcar-faq-header:not(.collapsed) .mxcar-faq-icon-wrap svg {
    stroke: #B03A2E !important;
}
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

/* Contact Page Matched Buttons */
.mxcar-btn-red {
    background-color: #B03A2E !important;
    color: #ffffff !important;
    border: none !important;
    padding: 16px 40px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    border-radius: 50px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    text-transform: uppercase !important;
    font-size: 0.95rem !important;
    letter-spacing: 0.5px !important;
}
.mxcar-btn-red:hover {
    background-color: #8E2B21 !important; 
    box-shadow: 0 6px 20px rgba(176, 58, 46, 0.25) !important;
    color: #fff !important;
}
.mxcar-btn-grey {
    background-color: transparent !important;
    color: #111111 !important;
    border: 1px solid #E9ECEF !important;
    padding: 15px 38px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    border-radius: 50px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    text-transform: uppercase !important;
    font-size: 0.95rem !important;
    letter-spacing: 0.5px !important;
}
.mxcar-btn-grey:hover {
    border-color: #B03A2E !important;
    color: #B03A2E !important;
    background-color: rgba(176, 58, 46, 0.05) !important;
}

.mxcar-page-title {
    color: #111111 !important;
    font-weight: 700 !important;
    font-size: 2.2rem !important;
}
.mxcar-page-desc {
    color: #6C757D !important;
    font-size: 1rem !important;
    max-width: 650px !important;
    margin: 0 auto !important;
}

/* Dark Mode Overrides */
[data-bs-theme="dark"] .mxcar-faq-clean-section {
    background-color: transparent !important; /* Theme background takes over */
}
[data-bs-theme="dark"] .mxcar-faq-box {
    background-color: #1a1a1a !important; /* Deep dark grey */
    border-color: #2e2e2e !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
}
[data-bs-theme="dark"] .mxcar-faq-box:hover {
    border-color: #2e2e2e !important; 
}
[data-bs-theme="dark"] a.mxcar-faq-header {
    background-color: #1a1a1a !important;
    color: #f1f1f1 !important;
}
[data-bs-theme="dark"] a.mxcar-faq-header:not(.collapsed) {
    background-color: #242424 !important; /* Mild highlight on active menu */
}
[data-bs-theme="dark"] .mxcar-faq-title {
    color: #f1f1f1 !important;
}
[data-bs-theme="dark"] a.mxcar-faq-header:not(.collapsed) .mxcar-faq-title {
    color: #B03A2E !important;
}
[data-bs-theme="dark"] .mxcar-faq-icon-wrap {
    background-color: #2e2e2e !important;
}
[data-bs-theme="dark"] .mxcar-faq-icon-wrap svg {
    stroke: #f1f1f1 !important;
}
[data-bs-theme="dark"] a.mxcar-faq-header:not(.collapsed) .mxcar-faq-icon-wrap {
    background-color: rgba(176, 58, 46, 0.2) !important;
}
[data-bs-theme="dark"] .mxcar-faq-body {
    border-top: 1px solid #2e2e2e !important;
    background-color: #242424 !important;
}
[data-bs-theme="dark"] .mxcar-faq-body p, 
[data-bs-theme="dark"] .mxcar-faq-body p * {
    color: #adb5bd !important;
}
[data-bs-theme="dark"] .mxcar-page-title {
    color: #f1f1f1 !important;
}
[data-bs-theme="dark"] .mxcar-page-desc {
    color: #adb5bd !important;
}
[data-bs-theme="dark"] .mxcar-btn-grey {
    color: #f1f1f1 !important;
    border-color: #444444 !important;
}
[data-bs-theme="dark"] .mxcar-btn-grey:hover {
    border-color: #B03A2E !important;
    color: #B03A2E !important;
    background-color: rgba(176, 58, 46, 0.1) !important;
}
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-faqs mxcar-faq-clean-section position-relative">
    <div class="container mxcar-faq-clean-container position-relative z-2">
        <div class="text-center mb-5 pb-3">
            @if($title = $shortcode->title)
                <h2 class="mb-3 mxcar-page-title">{!! BaseHelper::clean($title) !!}</h2>
            @endif

            @if ($description = $shortcode->description)
                <p class="mxcar-page-desc">{!! BaseHelper::clean($description) !!}</p>
            @endif
        </div>
        
        <div class="row g-4">
            @foreach($faqs as $faq)
                @php
                    $id = 'faq-item-' . md5($faq->question) . '-' . $faq->getKey();
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
            @endforeach
        </div>

        <div class="row">
            <div class="col-12 mt-5 pt-3">
                <div class="d-flex justify-content-center gap-3">
                    @if(($btnSecondaryLabel = $shortcode->button_secondary_label) && ($btnSecondaryUrl = $shortcode->button_secondary_url))
                        <a class="mxcar-btn-grey" href="{{ $btnSecondaryUrl }}">
                            {!! BaseHelper::clean($btnSecondaryLabel) !!}
                            <x-core::icon name="ti ti-arrow-right" class="svg-icon-arrow" />
                        </a>
                    @endif

                    @if (($btnPrimaryLabel = $shortcode->button_primary_label) && ($btnPrimaryUrl = $shortcode->button_primary_url))
                        <a class="mxcar-btn-red" href="{{ $btnPrimaryUrl }}">
                            {!! BaseHelper::clean($btnPrimaryLabel) !!}
                            <x-core::icon name="ti ti-arrow-right" class="svg-icon-arrow invert" />
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
