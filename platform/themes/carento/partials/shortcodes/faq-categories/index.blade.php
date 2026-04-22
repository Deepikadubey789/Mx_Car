@once
<style>
    /* =========================================
       PREMIUM FAQ CATEGORY CARDS
       ========================================= */
    .faq-category-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 40px 30px;
        text-align: center;
        border: 1px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    
    .faq-category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-color: var(--primary-color, #df4827);
    }

    .faq-icon-wrapper {
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        padding: 18px;
        transition: all 0.3s ease;
    }
    
    .faq-category-card:hover .faq-icon-wrapper {
        background: rgba(223, 72, 39, 0.08); 
        transform: scale(1.05);
    }

    .faq-icon-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .faq-cat-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 12px;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .faq-category-card:hover .faq-cat-title {
        color: var(--primary-color, #df4827);
    }

    .faq-cat-desc {
        font-size: 0.95rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 24px;
        flex-grow: 1;
    }

    .faq-cat-link {
        font-weight: 800;
        font-size: 0.85rem;
        color: var(--primary-color, #df4827);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: auto;
    }

    .faq-cat-link svg {
        transition: transform 0.3s ease;
    }
    .faq-category-card:hover .faq-cat-link svg {
        transform: translateX(6px); 
    }
    
    /* Dark Mode Support */
    html[data-bs-theme="dark"] .faq-category-card { background: #1e293b; border-color: #334155; }
    html[data-bs-theme="dark"] .faq-cat-title { color: #f8fafc; }
    html[data-bs-theme="dark"] .faq-cat-desc { color: #94a3b8; }
    html[data-bs-theme="dark"] .faq-icon-wrapper { background: #0f172a; }
    html[data-bs-theme="dark"] .faq-category-card:hover .faq-icon-wrapper { background: rgba(223, 72, 39, 0.15); }
</style>
@endonce

<section {!! $shortcode->htmlAttributes() !!} class="box-section background-body py-96 mt-40">
    <div class="container">
        
        <div class="text-center mb-5 pb-3">
            @if($title = $shortcode->title)
                <h2 class="fw-bolder mb-3 display-6" style="letter-spacing: -0.5px;">{{ BaseHelper::clean($title) }}</h2>
            @endif
            @if($description = $shortcode->description)
                <p class="text-lg-medium neutral-500 mx-auto" style="max-width: 600px;">{{ BaseHelper::clean($description) }}</p>
            @endif
        </div>
        
        <div class="row g-4">
            @foreach($faqCategories as $faqCategory)
                @continue(! $faqCategoryName = $faqCategory->name)

                @php
                    $faqCategoryLogo = $faqCategory->getMetaData('logo', true);
                    $faqCategoryLogoDark = $faqCategory->getMetaData('logo_dark', true);

                    // FIX: We now point directly to the custom route we created in web.php!
                    $categoryUrl = route('public.faq-category', $faqCategory->id);
                @endphp

                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="faq-category-card">
                        
                        @if(!empty($faqCategoryLogo) || !empty($faqCategoryLogoDark))
                            <div class="faq-icon-wrapper">
                                @if($faqCategoryLogo)
                                    {{ RvMedia::image($faqCategoryLogo, $faqCategoryName, attributes: ['class' => 'light-mode']) }}
                                @endif
                                @if($faqCategoryLogoDark)
                                    {{ RvMedia::image($faqCategoryLogoDark, $faqCategoryName, attributes: ['class' => 'dark-mode']) }}
                                @endif
                            </div>
                        @endif
                        
                        <a href="{{ $categoryUrl }}" class="faq-cat-title">
                            {!! BaseHelper::clean($faqCategoryName) !!}
                        </a>
                        
                        @if($description = $faqCategory->description)
                            <p class="faq-cat-desc">{!! BaseHelper::clean($description) !!}</p>
                        @endif
                        
                        <a class="faq-cat-link" href="{{ $categoryUrl }}">
                            {{ __('Details') }}
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>

                    </div>
                </div>
            @endforeach
        </div>
        
    </div>
</section>