<!-- @php
    $bgImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $bgImageTablet = $shortcode->background_image_tablet ? RvMedia::getImageUrl($shortcode->background_image_tablet) : null;
    $bgImageMobile = $shortcode->background_image_mobile ? RvMedia::getImageUrl($shortcode->background_image_mobile) : null;
    $variablesStyle = [
        "--background-image: url($bgImage)" => $bgImage,
        "--background-image-tablet: url(" . ($bgImageTablet ?: $bgImage) . ")" => $bgImageTablet || $bgImage,
        "--background-image-mobile: url(" . ($bgImageMobile ?: $bgImageTablet ?: $bgImage) . ")" => $bgImageMobile || $bgImageTablet || $bgImage,
    ];
@endphp

<style>
    .custom-hero-wrapper {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    @keyframes kenBurns {
        0%   { background-size: 105%; background-position: center center; }
        50%  { background-size: 118%; background-position: center top; }
        100% { background-size: 105%; background-position: center center; }
    }

    @keyframes shimmer {
        0%, 100% { opacity: 0.65; }
        50%       { opacity: 0.88; }
    }

    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(40px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .custom-hero-v2 {
        /* background-size aur position animation handle karega */
        background-size: 105%;
        background-position: center center;
        background-repeat: no-repeat;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding-top: 40px;
        padding-bottom: 120px;
        animation: kenBurns 14s ease-in-out infinite;
    }

    .hero-gradient-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.4) 100%);
        z-index: 0;
        animation: shimmer 6s ease-in-out infinite;
    }

    .hero-badge {
        display: inline-block;
        background-color: #d84a38;
        color: #ffffff;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(216, 74, 56, 0.3);
        animation: slideInUp 0.6s ease 0.1s forwards;
        opacity: 0;
    }

    .custom-hero-v2 h1.hero-main-title,
.custom-hero-v2 h1.hero-main-title *,
.custom-hero-v2 .hero-main-title p,
.custom-hero-v2 .hero-main-title span,
.custom-hero-v2 .hero-main-title strong {
    font-size: clamp(1.1rem, 2.2vw, 1.8rem) !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    line-height: 1.2 !important;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2) !important;
}

    .custom-hero-v2 h1.hero-main-title {
        margin-bottom: 2rem !important;
        animation: slideInUp 0.8s ease 0.3s forwards;
        opacity: 0;
    }

    .hero-features-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
        padding: 0;
        list-style: none;
        animation: slideInUp 0.8s ease 0.5s forwards;
        opacity: 0;
    }

    .hero-feature-pill {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        color: #ffffff;
        font-weight: 500;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .hero-feature-pill:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .hero-feature-pill svg {
        color: #d84a38;
    }
</style>

<div class="container custom-hero-wrapper">
    <section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!}
        class="custom-hero-v2 position-relative"
    >
        <div class="hero-gradient-overlay"></div>

        <div class="container position-relative z-1 text-center">

            <div>
                <span class="hero-badge">{{ __('Find Your Perfect Car') }}</span>
            </div>

            @if ($title = $shortcode->title)
                <h1 class="hero-main-title text-white color-white">
                    {!! BaseHelper::clean($title) !!}
                </h1>
            @endif

            <ul class="hero-features-wrap">
                @foreach($tabs as $tab)
                    @continue(! $content = Arr::get($tab, 'content'))
                    <li class="hero-feature-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
                        </svg>
                        {!! BaseHelper::clean($content) !!}
                    </li>
                @endforeach
            </ul>

        </div>
    </section>
</div>  -->


@php
    $bgImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $bgImageTablet = $shortcode->background_image_tablet ? RvMedia::getImageUrl($shortcode->background_image_tablet) : null;
    $bgImageMobile = $shortcode->background_image_mobile ? RvMedia::getImageUrl($shortcode->background_image_mobile) : null;
    $variablesStyle = [
        "--background-image: url($bgImage)" => $bgImage,
        "--background-image-tablet: url(" . ($bgImageTablet ?: $bgImage) . ")" => $bgImageTablet || $bgImage,
        "--background-image-mobile: url(" . ($bgImageMobile ?: $bgImageTablet ?: $bgImage) . ")" => $bgImageMobile || $bgImageTablet || $bgImage,
    ];
@endphp

<style>
    .custom-hero-wrapper {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    /* ── Glitch CSS Variables ── */
    .custom-hero-v2 {
        --gap-horizontal: 8px;
        --gap-vertical:   4px;
        --time-anim:      5s;
        --delay-anim:     1s;
        --blend-color-5:  #af4949;
    }

    /* ── Overlay shimmer ── */
    @keyframes shimmer {
        0%, 100% { opacity: 0.65; }
        50%       { opacity: 0.88; }
    }

    /* ── Animista: text-focus-in ── */
    @keyframes text-focus-in {
        0%   { filter: blur(10px); opacity: 0; }
        100% { filter: blur(0);    opacity: 1; }
    }

    /* ── Animista: tracking-in-expand ── */
    @keyframes tracking-in-expand {
        0%   { letter-spacing: -0.5em; opacity: 0; }
        40%  { opacity: 0.6; }
        100% { opacity: 1; }
    }

    /* ── Animista: scale-in-center ── */
    @keyframes scale-in-center {
        0%   { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* ── Badge fade ── */
    @keyframes fade-in-fwd {
        0%   { transform: translateY(16px) scale(0.92); opacity: 0; }
        100% { transform: translateY(0)    scale(1);    opacity: 1; }
    }

    /* ── Glitch image layers ── */
    @keyframes glitch-anim-2 {
        0%    { opacity:1; transform:translate3d(var(--gap-horizontal),0,0);
                clip-path:polygon(0 2%,100% 2%,100% 5%,0 5%); }
        4%    { clip-path:polygon(0 10%,100% 10%,100% 20%,0 20%); }
        8%    { clip-path:polygon(0 33%,100% 33%,100% 33%,0 33%); }
        12%   { clip-path:polygon(0 50%,100% 50%,100% 20%,0 20%); }
        18%   { clip-path:polygon(0 50%,100% 50%,100% 55%,0 55%); }
        21.9% { opacity:1; transform:translate3d(var(--gap-horizontal),0,0); }
        22%,100% { opacity:0; transform:translate3d(0,0,0); clip-path:polygon(0 0,0 0,0 0,0 0); }
    }

    @keyframes glitch-anim-3 {
        0%    { opacity:1; transform:translate3d(calc(-1 * var(--gap-horizontal)),0,0);
                clip-path:polygon(0 25%,100% 25%,100% 30%,0 30%); }
        5%    { clip-path:polygon(0 5%,100% 5%,100% 20%,0 20%); }
        11%   { clip-path:polygon(0 52%,100% 52%,100% 59%,0 59%); }
        17%   { clip-path:polygon(0 65%,100% 65%,100% 40%,0 40%); }
        21.9% { opacity:1; transform:translate3d(calc(-1 * var(--gap-horizontal)),0,0); }
        22%,100% { opacity:0; transform:translate3d(0,0,0); clip-path:polygon(0 0,0 0,0 0,0 0); }
    }

    @keyframes glitch-anim-4 {
        0%    { opacity:1; transform:translate3d(0,calc(-1 * var(--gap-vertical)),0) scale3d(-1,-1,1);
                clip-path:polygon(0 1%,100% 1%,100% 3%,0 3%); }
        5%    { clip-path:polygon(0 30%,100% 30%,100% 25%,0 25%); }
        11%   { clip-path:polygon(0 70%,100% 70%,100% 69%,0 69%); }
        18%   { clip-path:polygon(0 100%,100% 100%,100% 99%,0 99%); }
        21.9% { opacity:1; transform:translate3d(0,calc(-1 * var(--gap-vertical)),0) scale3d(-1,-1,1); }
        22%,100% { opacity:0; transform:translate3d(0,0,0); clip-path:polygon(0 0,0 0,0 0,0 0); }
    }

    @keyframes glitch-anim-flash {
        0%,5%     { opacity:0.25; transform:translate3d(var(--gap-horizontal),var(--gap-vertical),0); }
        5.5%,100% { opacity:0;    transform:translate3d(0,0,0); }
    }

    /* ── Glitch text animation ── */
    @keyframes glitch-text-before {
        0%,100% { clip-path:polygon(0 8%,100% 8%,100% 14%,0 14%);  transform:translate(-2px,0); }
        25%     { clip-path:polygon(0 38%,100% 38%,100% 43%,0 43%); transform:translate(2px,0); }
        50%     { clip-path:polygon(0 62%,100% 62%,100% 68%,0 68%); transform:translate(-2px,0); }
        75%     { clip-path:polygon(0 22%,100% 22%,100% 27%,0 27%); transform:translate(2px,0); }
    }

    @keyframes glitch-text-after {
        0%,100% { clip-path:polygon(0 55%,100% 55%,100% 60%,0 60%); transform:translate(2px,0); }
        25%     { clip-path:polygon(0 15%,100% 15%,100% 20%,0 20%); transform:translate(-2px,0); }
        50%     { clip-path:polygon(0 80%,100% 80%,100% 86%,0 86%); transform:translate(2px,0); }
        75%     { clip-path:polygon(0 44%,100% 44%,100% 50%,0 50%); transform:translate(-2px,0); }
    }

    /* ── Hero section ── */
    .custom-hero-v2 {
        position: relative;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        padding-top: 40px;
        padding-bottom: 100px;
    }

    /* ── 5 glitch image layers ── */
    .glitch-layer {
        position: absolute;
        inset: 0;
        background-image: var(--background-image);
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
    }

    .glitch-layer:nth-child(1) { opacity: 1; z-index: 0; }

    .glitch-layer:nth-child(n+2) {
        opacity: 0;
        animation-duration: var(--time-anim);
        animation-delay: var(--delay-anim);
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }
    .glitch-layer:nth-child(2) { animation-name: glitch-anim-2; }
    .glitch-layer:nth-child(3) { animation-name: glitch-anim-3; }
    .glitch-layer:nth-child(4) { animation-name: glitch-anim-4; }
    .glitch-layer:nth-child(5) {
        background-color: var(--blend-color-5);
        background-blend-mode: overlay;
        animation-name: glitch-anim-flash;
    }

    @media (max-width: 768px) {
        .glitch-layer { background-image: var(--background-image-tablet); }
    }
    @media (max-width: 480px) {
        .glitch-layer { background-image: var(--background-image-mobile); }
    }

    /* ── Dark overlay ── */
    .hero-gradient-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(15,23,42,0.80) 0%, rgba(15,23,42,0.40) 100%);
        z-index: 1;
        animation: shimmer 6s ease-in-out infinite;
    }

    /* ── Badge ── */
    .hero-badge {
        display: inline-block;
        background-color: #d84a38;
        color: #ffffff;
        padding: 0.45rem 1.4rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 1.2rem;
        box-shadow: 0 4px 15px rgba(216,74,56,0.35);
        animation: fade-in-fwd 0.7s cubic-bezier(.39,.575,.565,1) 0.2s both;
    }

    
    /* ── Title with glitch text ── */
    .custom-hero-v2 h1.hero-main-title {
        position: relative;
        display: inline-block;
        font-size: clamp(2.2rem, 4vw, 3.2rem) !important;  /* ← yahan change kiya */
        font-weight: 700 !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        line-height: 1.25 !important;
        text-shadow: 0 2px 12px rgba(0,0,0,0.3) !important;
        margin-bottom: 1.6rem !important;
        animation: text-focus-in 1s cubic-bezier(.55,.085,.68,.53) 0.5s both;
        letter-spacing: 0.02em !important;
    }

    .custom-hero-v2 h1.hero-main-title *,
    .custom-hero-v2 .hero-main-title p,
    .custom-hero-v2 .hero-main-title span,
    .custom-hero-v2 .hero-main-title strong {
        font-size: clamp(2.2rem, 4vw, 3.2rem) !important;  /* ← yahan bhi */
        font-weight: 700 !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        line-height: 1.25 !important;
    }

    /* Glitch ::before (cyan shadow) */
    .custom-hero-v2 h1.hero-main-title::before {
        content: attr(data-text);
        position: absolute;
        left: 0; top: 0;
        width: 100%; height: 100%;
        color: #ffffff;
        -webkit-text-fill-color: #ffffff;
        text-shadow: -2px 0 #00ffea;
        overflow: hidden;
        animation: glitch-text-before 3s linear infinite;
        animation-delay: 0.8s;
    }

    /* Glitch ::after (pink shadow) */
    .custom-hero-v2 h1.hero-main-title::after {
        content: attr(data-text);
        position: absolute;
        left: 0; top: 0;
        width: 100%; height: 100%;
        color: #ffffff;
        -webkit-text-fill-color: #ffffff;
        text-shadow: 2px 0 #fe3a7f;
        overflow: hidden;
        animation: glitch-text-after 3s linear infinite;
        animation-delay: 1.2s;
    }

    /* ── Feature pills ── */
    .hero-features-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.85rem;
        margin-top: 0.5rem;
        padding: 0;
        list-style: none;
        animation: tracking-in-expand 0.9s cubic-bezier(.215,.61,.355,1) 1s both;
    }

    .hero-feature-pill {
        background: rgba(255,255,255,0.10);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.22);
        padding: 0.65rem 1.3rem;
        border-radius: 50px;
        color: #ffffff;
        font-weight: 500;
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background .3s, transform .3s, border-color .3s;
    }
    .hero-feature-pill:hover {
        background: rgba(255,255,255,0.20);
        transform: translateY(-3px);
        border-color: rgba(255,255,255,0.42);
    }
    .hero-feature-pill svg { color: #d84a38; flex-shrink: 0; }

    .hero-buttons-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
        animation: scale-in-center 0.6s cubic-bezier(.25,.46,.45,.94) 1.4s both;
    }

    .hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.8rem;
        border-radius: 50px;
        font-size: 0.92rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: transform .25s, box-shadow .25s, background .25s;
        border: none;
        letter-spacing: 0.3px;
    }

    .hero-btn-primary {
        background: #d84a38;
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(216,74,56,0.40);
    }
    .hero-btn-primary:hover {
        background: #c0392b;
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(216,74,56,0.55);
        color: #ffffff;
        text-decoration: none;
    }

    .hero-btn-ghost {
        background: rgba(255,255,255,0.10);
        color: #ffffff;
        border: 1.5px solid rgba(255,255,255,0.55);
        backdrop-filter: blur(8px);
    }
    .hero-btn-ghost:hover {
        background: rgba(255,255,255,0.22);
        border-color: rgba(255,255,255,0.85);
        transform: translateY(-3px);
        color: #ffffff;
        text-decoration: none;
    }

    .hero-btn-dark {
        background: rgba(15,23,42,0.75);
        color: #ffffff;
        border: 1.5px solid rgba(255,255,255,0.18);
        backdrop-filter: blur(8px);
    }
    .hero-btn-dark:hover {
        background: rgba(15,23,42,0.95);
        transform: translateY(-3px);
        border-color: rgba(255,255,255,0.38);
        color: #ffffff;
        text-decoration: none;
    }

    /*-----------------*/
    /* ── Neon Buttons ── */
    @keyframes neon-border-anim {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }
    @keyframes neon-dot-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50%       { transform: scale(1.4); opacity: 0.6; }
    }
    @keyframes neon-live-pulse {
        0%, 100% { box-shadow: 0 0 4px #22c55e; }
        50%       { box-shadow: 0 0 14px #22c55e, 0 0 22px #22c55e; }
    }
    @keyframes neon-star-spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .hero-neon-buttons-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
        animation: scale-in-center 0.6s cubic-bezier(.25,.46,.45,.94) 1.4s both;
    }

    .neon-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-size: 0.92rem;
        font-weight: 600;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.10);   /* ← yhi tha pehle hero-feature-pill jaisa */
        backdrop-filter: blur(10px);               /* ← glassmorphism */
        -webkit-backdrop-filter: blur(10px);
        text-decoration: none;
        cursor: pointer;
        letter-spacing: 0.4px;
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
        z-index: 0;
    }

    /* animated gradient border on hover */
    .neon-btn::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 50px;
        background-size: 200%;
        animation: neon-border-anim 3s linear infinite;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .neon-btn:hover::before { opacity: 1; }
    .neon-btn:hover {
        transform: translateY(-3px);
        color: #ffffff;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.18);   /* ← hover pe thoda bright */
    }
/* ── Button 1: Red — High quality at a low cost ── */
.neon-btn-purple {
        border: 1.5px solid #d84a38;
        box-shadow: 0 0 10px rgba(216,74,56,0.5), inset 0 0 10px rgba(216,74,56,0.08);
    }
    .neon-btn-purple::before {
        background: linear-gradient(90deg, #d84a38, #ff6b52, #d84a38);
    }
    .neon-btn-purple:hover {
        box-shadow: 0 0 22px rgba(216,74,56,0.7), 0 0 50px rgba(216,74,56,0.25);
    }
    .neon-btn-purple .neon-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #d84a38;
        box-shadow: 0 0 8px #d84a38;
        animation: neon-dot-pulse 1.5s ease-in-out infinite;
        flex-shrink: 0;
    }

    /* ── Button 2: Red — Premium services ── */
    .neon-btn-cyan {
        border: 1.5px solid #d84a38;
        box-shadow: 0 0 10px rgba(216,74,56,0.5), inset 0 0 10px rgba(216,74,56,0.08);
    }
    .neon-btn-cyan::before {
        background: linear-gradient(90deg, #d84a38, #ff6b52, #d84a38);
    }
    .neon-btn-cyan:hover {
        box-shadow: 0 0 22px rgba(216,74,56,0.7), 0 0 50px rgba(216,74,56,0.25);
    }
    .neon-btn-cyan .neon-star {
        font-size: 14px;
        display: inline-block;
        animation: neon-star-spin 4s linear infinite;
        flex-shrink: 0;
    }

    /* ── Button 3: Red — 24/7 roadside support ── */
    .neon-btn-green {
        border: 1.5px solid #d84a38;
        box-shadow: 0 0 10px rgba(216,74,56,0.5), inset 0 0 10px rgba(216,74,56,0.08);
    }
    .neon-btn-green::before {
        background: linear-gradient(90deg, #d84a38, #ff6b52, #d84a38);
    }
    .neon-btn-green:hover {
        box-shadow: 0 0 22px rgba(216,74,56,0.7), 0 0 50px rgba(216,74,56,0.25);
    }
    .neon-btn-green .neon-live {
        width: 8px; height: 8px; border-radius: 50%;
        background: #d84a38;
        box-shadow: 0 0 8px #d84a38;
        animation: neon-live-pulse 1s ease-in-out infinite;
        flex-shrink: 0;
    }
    /* Button 1 */
    .neon-btn-purple {
        border: 1.5px solid #d84a38;
        box-shadow: 0 0 8px #d84a38,
                    0 0 25px #d84a38,
                    0 0 60px rgba(216,74,56,0.6),
                    0 0 100px rgba(216,74,56,0.3);
    }
    .neon-btn-purple::before {
        background: linear-gradient(90deg, #d84a38, #ff6b52, #d84a38);
    }
    .neon-btn-purple:hover {
        box-shadow: 0 0 12px #d84a38,
                    0 0 40px #d84a38,
                    0 0 90px rgba(216,74,56,0.7),
                    0 0 150px rgba(216,74,56,0.4);
    }

    /* Button 2 */
    .neon-btn-cyan {
        border: 1.5px solid #d84a38;
        box-shadow: 0 0 8px #d84a38,
                    0 0 25px #d84a38,
                    0 0 60px rgba(216,74,56,0.6),
                    0 0 100px rgba(216,74,56,0.3);
    }
    .neon-btn-cyan::before {
        background: linear-gradient(90deg, #d84a38, #ff6b52, #d84a38);
    }
    .neon-btn-cyan:hover {
        box-shadow: 0 0 12px #d84a38,
                    0 0 40px #d84a38,
                    0 0 90px rgba(216,74,56,0.7),
                    0 0 150px rgba(216,74,56,0.4);
    }

    /* Button 3 */
    .neon-btn-green {
        border: 1.5px solid #d84a38;
        box-shadow: 0 0 8px #d84a38,
                    0 0 25px #d84a38,
                    0 0 60px rgba(216,74,56,0.6),
                    0 0 100px rgba(216,74,56,0.3);
    }
    .neon-btn-green::before {
        background: linear-gradient(90deg, #d84a38, #ff6b52, #d84a38);
    }
    .neon-btn-green:hover {
        box-shadow: 0 0 12px #d84a38,
                    0 0 40px #d84a38,
                    0 0 90px rgba(216,74,56,0.7),
                    0 0 150px rgba(216,74,56,0.4);
    }
        
    @keyframes kenBurns {
        0%   { background-size: 110%; background-position: center center; }
        33%  { background-size: 125%; background-position: 60% 40%; }
        66%  { background-size: 118%; background-position: 40% 60%; }
        100% { background-size: 110%; background-position: center center; }
    }

    .glitch-layer:nth-child(1) {
        opacity: 1;
        z-index: 0;
        animation: kenBurns 18s ease-in-out infinite;
    }
        .glitch-layer:nth-child(n+2) {
        opacity: 0;
        background-size: cover; /* glitch snap layers ke liye cover theek hai */
        animation-duration: var(--time-anim);
        animation-delay: var(--delay-anim);
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }
    </style>

<div class="container custom-hero-wrapper">
    <section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!}
        class="custom-hero-v2"
    >
        {{-- 5 Glitch background layers --}}
        <div class="glitch-layer"></div>
        <div class="glitch-layer"></div>
        <div class="glitch-layer"></div>
        <div class="glitch-layer"></div>
        <div class="glitch-layer"></div>

        {{-- Dark overlay --}}
        <div class="hero-gradient-overlay"></div>

        <div class="container position-relative text-center" style="z-index:2;">

            {{-- Badge --}}
            <div>
                <span class="hero-badge">{{ __('Find Your Perfect Car') }}</span>
            </div>

            {{-- Title with glitch pseudo-layers --}}
            @if ($title = $shortcode->title)
                <h1 class="hero-main-title text-white" data-text="{{ strip_tags($title) }}">
                    {!! BaseHelper::clean($title) !!}
                </h1>
            @endif

            {{-- Feature Pills --}}
            <!-- <ul class="hero-features-wrap">
                @foreach($tabs as $tab)
                    @continue(! $content = Arr::get($tab, 'content'))
                    <li class="hero-feature-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
                        </svg>
                        {!! BaseHelper::clean($content) !!}
                    </li>
                @endforeach
            </ul> -->

            {{-- Neon Buttons --}}
            <div class="hero-neon-buttons-wrap">
                <a href="#" class="neon-btn neon-btn-purple">
                    <span class="neon-dot"></span>
                    {{ __('High quality at a low cost') }}
                </a>
                <a href="#" class="neon-btn neon-btn-cyan">
                    <span class="neon-star">✦</span>
                    {{ __('Premium services') }}
                </a>
                <a href="#" class="neon-btn neon-btn-green">
                    <span class="neon-live"></span>
                    {{ __('24/7 roadside support') }}
                </a>
            </div>
            
           
        </div>
    </section>
</div>