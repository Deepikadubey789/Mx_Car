@php
    $backgroundColor = $shortcode->background_color;
@endphp

@if(count($tabs))
    {{-- SCAMPED STYLES FOR PREMIUM STATS REDESIGN --}}
    <style>
        .premium-stats-section {
            padding-top: 80px;
            padding-bottom: 80px;
            background-color: var(--background-color, transparent);
        }

        /* Floating Capsule Wrapper */
        .stats-capsule {
            background: #ffffff;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.04);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            position: relative;
            z-index: 10;
        }

        /* Individual Stat Block */
        .stat-item {
            position: relative;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 15px;
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        /* Subtle Vertical Dividers (Desktop Only) */
        @media (min-width: 992px) {
            .stat-item:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 15%;
                right: -15px;
                height: 70%;
                width: 1px;
                background-color: rgba(0, 0, 0, 0.08);
            }
        }

        /* Massive Typography for Numbers */
        .stat-number-wrapper {
            display: flex;
            align-items: baseline;
            justify-content: center;
            margin-bottom: 12px;
        }

        .stat-number {
            font-size: 4rem;
            font-weight: 800;
            color: #111827;
            line-height: 1;
            padding-right: 6px; /* Prevents odometer.js from clipping curved digits */
        }

        /* Accent color for the unit (e.g., K, +, %) */
        .stat-unit {
            font-size: 2rem;
            font-weight: 700;
            color: #df4827; /* Brand Orange/Red */
            margin-left: 2px;
        }

        /* Clean, tracked-out subtitle */
        .stat-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0;
        }

        /* Dark Mode Support */
        html[data-bs-theme="dark"] .stats-capsule,
        html[data-theme="dark"] .stats-capsule {
            background: #18181b;
            border-color: rgba(255, 255, 255, 0.05);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        html[data-bs-theme="dark"] .stat-number,
        html[data-theme="dark"] .stat-number { color: #ffffff; }
        html[data-bs-theme="dark"] .stat-item:not(:last-child)::after,
        html[data-theme="dark"] .stat-item:not(:last-child)::after { background-color: rgba(255, 255, 255, 0.1); }
    </style>

    <section {!! $shortcode->htmlAttributes(['style' => ["--background-color: $backgroundColor" => $backgroundColor]]) !!} 
        class="shortcode-site-statistics premium-stats-section"
    >
        <div class="container">
            <div class="stats-capsule wow fadeInUp" data-wow-delay="0.1s">
                
                @foreach($tabs as $item)
                    @php
                        $title = Arr::get($item, 'title');
                        $data = Arr::get($item, 'data');
                    @endphp
                    @continue(! $data || ! $title)
                    
                    <div class="stat-item">
                        <div class="stat-number-wrapper">
                            {{-- Kept the 'odometer' class so your theme's JS counting animation still works --}}
                            <div class="stat-number odometer" data-count="{{ $data }}">0</div>
                            
                            @if ($unit = Arr::get($item, 'unit'))
                                <div class="stat-unit">{!! BaseHelper::clean($unit) !!}</div>
                            @endif
                        </div>
                        <p class="stat-title">{!! BaseHelper::clean($title) !!}</p>
                    </div>
                    
                @endforeach
                
            </div>
        </div>
    </section>
@endif