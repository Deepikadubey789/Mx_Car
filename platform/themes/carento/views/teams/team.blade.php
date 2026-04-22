@php
    Theme::set('breadcrumbs', true);
    Theme::set('breadcrumb_simple', true);
@endphp

<style>
    /* Premium Profile Sidebar Styling */
    .profile-sidebar-sticky {
        position: sticky;
        top: 120px; /* Keeps the profile pinned while scrolling the bio */
    }
    
    .team-avatar-large {
        width: 280px;
        height: 280px;
        border-radius: 50%;
        overflow: hidden;
        border: 8px solid #ffffff;
        box-shadow: 0 15px 35px rgba(0,0,0,0.06);
        margin: 0 auto;
    }

    .contact-bento-card {
        background: #f8f9fa;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .social-bubble {
        width: 44px;
        height: 44px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        transition: all 0.2s ease;
    }
    
    .social-bubble:hover {
        background: var(--primary-color, #df4827);
        color: #ffffff;
        border-color: var(--primary-color, #df4827);
        transform: translateY(-3px);
    }
</style>

<div class="team-detail-page">
    <section class="section-box background-body py-5">
        <div class="container">
            <div class="row align-items-start">
                
                <div class="col-lg-4 mb-5 mb-lg-0 text-center">
                    <div class="profile-sidebar-sticky">
                        
                        @if ($photo = $team->photo)
                            <div class="team-avatar-large mb-4">
                                {{ RvMedia::image($photo, $team->name ?: $team->title, 'large-rectangle', attributes: ['class' => 'w-100 h-100', 'style' => 'object-fit: cover; object-position: top center;']) }}
                            </div>
                        @endif

                        @if($team->socials && count($team->socials) > 0)
                            <div class="d-flex justify-content-center gap-2 mb-4">
                                @foreach($team->socials as $key => $social)
                                    <a href="{{ $social }}" class="rounded-circle d-flex align-items-center justify-content-center social-bubble" target="_blank" rel="noopener noreferrer" title="{{ ucfirst($key) }}">
                                        <x-core::icon name="ti ti-brand-{{ $key }}" class="m-0" style="width: 20px; height: 20px;" />
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="contact-bento-card text-start shadow-sm mt-2">
                            <h5 class="fw-bold mb-4" style="font-size: 1.1rem; color: #111827;">{{ __('Contact Information') }}</h5>
                            
                            @if ($phone = $team->phone)
                                <div class="d-flex align-items-center mb-4">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(223, 72, 39, 0.1); color: var(--primary-color, #df4827);">
                                        <x-core::icon name="ti ti-phone" />
                                    </div>
                                    <div>
                                        <span class="d-block" style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Phone') }}</span>
                                        <a href="tel:{{ $phone }}" class="fw-bold text-decoration-none" style="color: #111827; font-size: 1rem;" dir="ltr">{{ $phone }}</a>
                                    </div>
                                </div>
                            @endif

                            @if ($email = $team->email)
                                <div class="d-flex align-items-center mb-4">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(223, 72, 39, 0.1); color: var(--primary-color, #df4827);">
                                        <x-core::icon name="ti ti-mail" />
                                    </div>
                                    <div class="text-truncate">
                                        <span class="d-block" style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Email') }}</span>
                                        {!! Html::mailto($email, attributes: ['class' => 'fw-bold text-decoration-none text-truncate', 'style' => 'color: #111827; font-size: 1rem; display: block;', 'dir' => 'ltr']) !!}
                                    </div>
                                </div>
                            @endif

                            @if ($address = $team->address)
                                <div class="d-flex align-items-start">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; background: rgba(223, 72, 39, 0.1); color: var(--primary-color, #df4827);">
                                        <x-core::icon name="ti ti-map-pin" />
                                    </div>
                                    <div>
                                        <span class="d-block" style="font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Address') }}</span>
                                        <span class="fw-bold" style="color: #111827; font-size: 0.95rem; line-height: 1.4;">{{ $address }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 ps-lg-5 pt-lg-3">
                    <div class="team-details-content">
                        
                        <div class="mb-5 border-bottom pb-4">
                            <h1 class="fw-bold mb-2" style="color: #111827; font-size: 3rem; letter-spacing: -1px;">{{ $team->name }}</h1>
                            
                            @if ($title = $team->title)
                                <span class="d-inline-block px-3 py-1 fw-bold rounded-pill mb-4" style="background: #f8f9fa; color: var(--primary-color, #df4827); font-size: 0.9rem; border: 1px solid #e5e7eb;">
                                    {{ $title }}
                                </span>
                            @endif

                            @if ($description = $team->description)
                                <p class="lead" style="color: #4b5563; font-size: 1.2rem; line-height: 1.7;">{{ $description }}</p>
                            @endif
                        </div>

                        @if($team->content)
                            <div class="team-content" style="color: #374151; font-size: 1.05rem; line-height: 1.8;">
                                <div class="content-detail ck-content">
                                    {!! BaseHelper::clean($team->content) !!}
                                </div>
                            </div>
                        @endif
                        
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    @if(dynamic_sidebar('service_detail_bottom_sidebar'))
        <section class="section-box background-body border-top py-96">
            <div class="container">
                {!! dynamic_sidebar('service_detail_bottom_sidebar') !!}
            </div>
        </section>
    @endif
</div>