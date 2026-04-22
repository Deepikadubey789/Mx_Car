@php
    // Fetch published Guest Protection Plans
    $guestPlans = \Botble\CarRentals\Models\GuestProtectionPlan::query()
        ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
        ->get();

    // Fetch published Host Protection Plans
    $hostPlans = \Botble\CarRentals\Models\HostProtectionPlan::query()
        ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
        ->get();
@endphp

@once
<style>
    /* Smooth fade-in animation for switching tabs */
    @keyframes smoothFadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .plan-section-active {
        display: flex !important;
        animation: smoothFadeInUp 0.4s ease forwards;
    }
    .plan-section-hidden {
        display: none !important;
    }
</style>
@endonce

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-pricing section-pricing-1 pt-80 pb-100 background-body border-bottom">
    <div class="container">
        
        <div class="row pb-4 mb-5 z-1 justify-content-center"> <div class="col-lg-auto align-self-end text-center">
                @if ($title = $shortcode->title)
                    <h2 class="heading-3 shortcode-title">{!! BaseHelper::clean($title) !!}</h2>
                @endif
                @if ($description = $shortcode->description)
                    <p class="text-muted mt-2">{!! BaseHelper::clean($description) !!}</p>
                @endif

                <div class="d-flex justify-content-center align-items-center mt-4 pb-3"> <ul class="list-unstyled d-flex align-items-center gap-3 mb-0">
                        <li>
                            <a href="#" class="active btn btn-primary px-4 py-2 custom-plan-toggle" data-target="guest-plans-container">
                                {{ __('Guest Plans') }}
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-white border px-4 py-2 custom-plan-toggle" data-target="host-plans-container">
                                {{ __('Host Plans') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row justify-content-center plan-section-active" id="guest-plans-container">
            @if($guestPlans->isNotEmpty())
                @foreach($guestPlans as $plan)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="h-100 p-4 border rounded-12 bg-white hover-up transition-all">
                            <h6 class="text-lg-bold neutral-1000">{!! BaseHelper::clean($plan->name) !!}</h6>
                            
                            <div class="d-flex align-items-baseline mt-2">
                                <span class="heading-3 neutral-1000 mb-0">$</span>
                                <h3 class="neutral-1000 mb-0">{{ number_format($plan->daily_fee, 2) }}</h3>
                                <span class="neutral-500 text-md-medium ms-2">{{ __('/ Day') }}</span>
                            </div>

                            @if ($plan->description)
                                <p class="text-sm-medium neutral-500 mt-3">{!! BaseHelper::clean($plan->description) !!}</p>
                            @endif

                            <ul class="list-unstyled mb-0 py-4 border-top mt-4">
                                <li class="d-flex align-items-center mb-3 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon flex-shrink-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                                    <p class="text-sm-medium neutral-1000 m-0 ms-2">
                                        <strong>{{ __('Deductible:') }}</strong> ${{ number_format($plan->deductible_amount, 2) }}
                                    </p>
                                </li>
                                <li class="d-flex align-items-center mb-3 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon flex-shrink-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                                    <p class="text-sm-medium neutral-1000 m-0 ms-2">
                                        <strong>{{ __('Liability Limit:') }}</strong> ${{ number_format($plan->liability_limit, 2) }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center py-4">
                    <p class="text-muted">{{ __('No guest plans available at the moment.') }}</p>
                </div>
            @endif
        </div>

        <div class="row justify-content-center plan-section-hidden" id="host-plans-container">
            @if($hostPlans->isNotEmpty())
                @foreach($hostPlans as $plan)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="h-100 p-4 border rounded-12 bg-white hover-up transition-all">
                            <h6 class="text-lg-bold neutral-1000">{!! BaseHelper::clean($plan->name) !!}</h6>
                            
                            <div class="d-flex align-items-baseline mt-2">
                                <h3 class="neutral-1000 mb-0">{{ $plan->revenue_share_percentage }}</h3>
                                <span class="heading-3 neutral-1000 mb-0">%</span>
                                <span class="neutral-500 text-md-medium ms-2">{{ __('Revenue Share') }}</span>
                            </div>

                            @if ($plan->description)
                                <p class="text-sm-medium neutral-500 mt-3">{!! BaseHelper::clean($plan->description) !!}</p>
                            @endif

                            <ul class="list-unstyled mb-0 py-4 border-top mt-4">
                                <li class="d-flex align-items-center mb-3 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon flex-shrink-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                                    <p class="text-sm-medium neutral-1000 m-0 ms-2">
                                        <strong>{{ __('Deductible:') }}</strong> ${{ number_format($plan->deductible_amount, 2) }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center py-4">
                    <p class="text-muted">{{ __('No host plans available at the moment.') }}</p>
                </div>
            @endif
        </div>

    </div>
    
    <div class="rotate-center ellipse-rotate-success position-absolute z-0"></div>
    <div class="rotate-center-rev ellipse-rotate-primary position-absolute z-0"></div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.custom-plan-toggle');
        const planSections = [
            document.getElementById('guest-plans-container'),
            document.getElementById('host-plans-container')
        ];

        toggleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // 1. Reset all buttons to inactive state (white border)
                toggleButtons.forEach(btn => {
                    btn.classList.remove('btn-primary', 'active');
                    btn.classList.add('btn-white', 'border');
                });

                // 2. Set clicked button to active state (primary color)
                this.classList.remove('btn-white', 'border');
                this.classList.add('btn-primary', 'active');

                // 3. Hide all sections, then reveal the target section
                const targetId = this.getAttribute('data-target');
                planSections.forEach(section => {
                    if (section) {
                        if (section.id === targetId) {
                            section.classList.remove('plan-section-hidden');
                            section.classList.add('plan-section-active');
                        } else {
                            section.classList.remove('plan-section-active');
                            section.classList.add('plan-section-hidden');
                        }
                    }
                });
            });
        });
    });
</script>