@use(Botble\Theme\Supports\Youtube;)

@php
Theme::set('breadcrumbs', false);
Theme::layout('full-width');

$youtubeUrl = $car->getMetaData('youtube_video_url', true);
$youtubeId = $youtubeUrl ? Youtube::getYoutubeVideoID($youtubeUrl) : null;
$images = $car->getImages();

$imgAll = $images;
$imgExterior = array_values(array_filter($images, fn($i) => str_starts_with(basename($i), 'ext_') || str_starts_with(basename($i), 'ext-')));
$img360 = array_values(array_filter($images, fn($i) => str_starts_with(basename($i), '360_') || str_starts_with(basename($i), '360-')));
$imgInterior = array_values(array_filter($images, fn($i) => str_starts_with(basename($i), 'int_') || str_starts_with(basename($i), 'int-')));

$colorGroups = [];
foreach ($images as $img) {
$base = basename($img);
if (str_starts_with($base, 'clr_')) {
$parts = explode('_', $base);
$colorKey = $parts[1] ?? 'default';
$colorGroups[$colorKey][] = $img;
}
}
@endphp

<div class="car-detail-page car-detail-page--style-1">
<style>
.car-detail-page--style-1,.car-detail-page--style-1 .background-body{background-color:#F4F6F8!important}
.car-detail-modern__content-shell,.box-content-tour-detail{padding-top:0!important;margin-top:0!important}
.car-detail-modern__header,.car-detail-modern__layout .col-lg-12>div,.car-detail-modern__sidebar-sticky>div{background-color:#fff!important;border:1px solid #E9ECEF!important;border-radius:20px!important;padding:28px 32px!important;box-shadow:0 4px 20px rgba(0,0,0,.03)!important;margin-bottom:20px}
.car-detail-page--style-1 h1,.car-detail-page--style-1 h2,.car-detail-page--style-1 h3,.car-detail-page--style-1 h4,.car-detail-page--style-1 h5,.car-detail-page--style-1 .neutral-1000{color:#111!important;font-weight:700!important}
.car-detail-modern__eyebrow{color:#B03A2E!important;font-size:.72rem!important;font-weight:700!important;text-transform:uppercase!important;letter-spacing:1.2px!important}
.car-detail-modern__quick-meta span{display:inline-flex;align-items:center;background:#F4F6F8!important;border:1px solid #E9ECEF!important;border-radius:20px!important;padding:4px 14px!important;font-size:.78rem!important;font-weight:600!important;color:#555!important;margin-right:6px;margin-top:6px}
.car-detail-modern__spec-pill,.item-feature-car-inner{background-color:#F8F9FA!important;border:1px solid #E9ECEF!important;border-radius:12px!important;padding:14px 18px!important;transition:border-color .2s,box-shadow .2s!important}
.car-detail-modern__spec-pill:hover,.item-feature-car-inner:hover{border-color:rgba(176,58,46,.4)!important;box-shadow:0 2px 8px rgba(176,58,46,.08)!important}
.car-detail-modern__spec-pill .feature-image svg,.car-detail-modern__spec-pill .feature-image i,.item-feature-car-inner .feature-image svg,.item-feature-car-inner .feature-image i{color:#B03A2E!important}
.car-detail-modern__spec-pill .neutral-1000,.item-feature-car-inner .neutral-1000{color:#111!important;font-weight:600!important}
.car-detail-modern__collapse-trigger{display:flex!important;align-items:center!important;justify-content:space-between!important;width:100%!important;background:transparent!important;border:none!important;padding:0!important;color:#111!important;font-weight:700!important}
.car-detail-modern__collapse-trigger strong{color:#111!important;font-size:1rem!important;font-weight:700!important}
.car-detail-modern__collapse-trigger svg path{stroke:#B03A2E!important}
.car-detail-modern__collapse-card{background-color:transparent!important;border:none!important;padding:16px 0 0!important}
.car-detail-modern__badge{display:inline-flex!important;align-items:center!important;background-color:#FFF5F5!important;color:#B03A2E!important;border:1px solid rgba(176,58,46,.2)!important;border-radius:6px!important;padding:3px 10px!important;font-size:.75rem!important;font-weight:600!important}
.car-detail-modern__info-grid .feature-image svg,.car-detail-modern__info-grid .feature-image i{color:#B03A2E!important}
.car-detail-modern__amenity-item{display:inline-flex!important;align-items:center!important;gap:8px!important;background-color:#F8F9FA!important;border:1px solid #E9ECEF!important;border-radius:8px!important;padding:8px 14px!important;margin:4px!important;list-style:none!important;font-size:.875rem!important;color:#333!important;transition:border-color .15s!important}
.car-detail-modern__amenity-item:hover{border-color:rgba(176,58,46,.35)!important}
.car-detail-modern__amenity-item svg,.car-detail-modern__amenity-item i{color:#B03A2E!important}
.car-detail-modern__collapse-card ul{display:flex!important;flex-wrap:wrap!important;list-style:none!important;padding:0!important;margin:0!important}
.car-detail-modern__amenity-category{color:#B03A2E!important;font-size:.8rem!important;font-weight:700!important;text-transform:uppercase!important;letter-spacing:.8px!important}
.car-detail-page--style-1 .icon-tabler-star,.car-detail-page--style-1 .rate-element svg,.car-detail-page--style-1 .icon-tabler-map-pin,.car-detail-page--style-1 .tour-location svg,.car-detail-page--style-1 .tour-location i{color:#B03A2E!important}
.car-detail-modern__gallery-main .wrapper-image img{border-radius:20px!important}
.car-detail-modern__gallery-thumbs .banner-slide img{border-radius:10px!important;border:2px solid transparent;transition:border-color .2s}
.car-detail-modern__gallery-thumbs .slick-current img{border-color:#B03A2E!important}
.wrapper-image img{transition:transform .4s ease}
.wrapper-image:hover img{transform:scale(1.08)}
.video-thumb-slide:hover img{transform:scale(1.05);transition:transform .3s ease}
.car-detail-modern__form-wrap{position:sticky;top:88px;background:#fff;border-radius:20px;border:1px solid #E9ECEF;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.07)}
.head-booking-form{background:linear-gradient(135deg,#B03A2E 0%,#8E2B21 100%)!important;margin:0!important;padding:16px 24px!important;border-radius:0!important;display:block!important}
.head-booking-form p,.head-booking-form .text-xl-bold{color:#fff!important;font-size:1.05rem!important;font-weight:700!important;margin:0!important}
.car-detail-modern__form-inner{padding:24px}
.booking-form--modern .content-booking-form div,.booking-form--modern .availability-check,.booking-form--modern .form-group,.booking-form--modern .mb-20{background-color:transparent!important;background:transparent!important;border:none!important;box-shadow:none!important}
.booking-form--modern label,.car-detail-modern__form-wrap label{color:#111!important;font-weight:600!important;font-size:.85rem!important;margin-bottom:8px!important;display:block!important}
.booking-form--modern .form-control,.booking-form--modern .form-select,.car-detail-modern__form-wrap .form-control,.car-detail-modern__form-wrap .form-select{background-color:#F8F9FA!important;border:1px solid #E9ECEF!important;border-radius:10px!important;color:#111!important;height:auto!important;padding:12px 16px!important}
.booking-form--modern .form-control:focus,.booking-form--modern .form-select:focus{border-color:#B03A2E!important;box-shadow:0 0 0 3px rgba(176,58,46,.1)!important}
.booking-form--modern .form-check-label{color:#333!important;font-size:.875rem!important}
.booking-form--modern .text-sm-bold,.booking-form--modern .text-heading-5,.car-detail-modern__form-wrap .text-end .heading-6{color:#111!important;font-weight:700!important}
.car-detail-page--style-1 .btn-book,.car-detail-page--style-1 .btn-primary,.car-detail-page--style-1 button[type="submit"].btn{background-color:#B03A2E!important;border-color:#B03A2E!important;color:#fff!important;font-weight:700!important;border-radius:10px!important;padding:14px 24px!important;letter-spacing:.3px!important;transition:all .25s ease!important;width:100%!important}
.car-detail-page--style-1 .btn-book:hover,.car-detail-page--style-1 .btn-primary:hover{background-color:#8E2B21!important;box-shadow:0 8px 24px rgba(176,58,46,.25)!important;transform:translateY(-2px)!important}
.car-detail-modern__header{margin-top:16px!important;margin-bottom:16px!important}
.car-detail-modern__faq-item{background-color:#F8F9FA!important;border:1px solid #E9ECEF!important;border-radius:16px!important;padding:20px 24px 20px 56px!important;margin-bottom:16px!important;position:relative}
.car-detail-modern__faq-item::before{content:'?';position:absolute!important;left:18px!important;top:20px!important;width:26px!important;height:26px!important;background-color:#B03A2E!important;color:#fff!important;border-radius:50%!important;display:flex!important;align-items:center!important;justify-content:center!important;font-size:.85rem!important;font-weight:700!important}
.car-detail-modern__faq-item .head-question p{color:#111!important;font-size:.95rem!important;font-weight:700!important;margin-bottom:8px!important}
.car-detail-modern__faq-item .content-question{color:#555!important;font-size:.875rem!important;line-height:1.6!important}
[data-bs-theme="dark"] .car-detail-page--style-1,[data-bs-theme="dark"] .car-detail-page--style-1 .background-body{background-color:#151515!important}
[data-bs-theme="dark"] .car-detail-modern__header,[data-bs-theme="dark"] .car-detail-modern__layout .col-lg-12>div,[data-bs-theme="dark"] .car-detail-modern__form-wrap{background-color:#1e1e1e!important;border-color:#2e2e2e!important}
[data-bs-theme="dark"] .car-detail-modern__spec-pill,[data-bs-theme="dark"] .item-feature-car-inner{background-color:#252525!important;border-color:#333!important}
[data-bs-theme="dark"] .car-detail-modern__amenity-item{background-color:#252525!important;border-color:#333!important;color:#ccc!important}
[data-bs-theme="dark"] .car-detail-modern__faq-item{background-color:#252525!important;border-color:#333!important}
[data-bs-theme="dark"] .car-detail-modern__faq-item .head-question p{color:#f1f1f1!important}
[data-bs-theme="dark"] .car-detail-modern__faq-item .content-question{color:#aaa!important}

#mxPhotoModal{display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.96);flex-direction:column}
#mxPhotoModal.open{display:flex}
.mxm-head{display:flex;align-items:center;justify-content:space-between;padding:10px 20px;background:#111;flex-shrink:0}
.mxm-title{color:#fff;font-size:16px;font-weight:600}
.mxm-close{background:none;border:none;color:#aaa;font-size:26px;cursor:pointer;line-height:1;padding:0 4px}
.mxm-close:hover{color:#fff}
.mxm-tabs{display:flex;gap:0;padding:0 20px;background:#1a1a1a;border-bottom:1px solid #333;overflow-x:auto;flex-shrink:0}
.mxm-tab{color:#888;font-size:13px;font-weight:500;padding:11px 16px;cursor:pointer;border-bottom:3px solid transparent;white-space:nowrap;transition:color .15s,border-color .15s;display:flex;align-items:center;gap:5px;background:none;border-left:none;border-right:none;border-top:none}
.mxm-tab.active{color:#fff;border-bottom-color:#5b9bf8}
.mxm-tab:hover:not(.active){color:#ccc}
.mxm-body{display:flex;flex:1;overflow:hidden}
.mxm-stage{position:relative;flex:1;background:#000;overflow:hidden;display:flex;align-items:center;justify-content:center}
.mxm-stage img.mx-main{max-width:100%;max-height:100%;object-fit:contain;display:none}
.mxm-stage img.mx-main.show{display:block}
.mxm-stage iframe{width:100%;height:100%;border:none;display:none}
.mxm-stage iframe.show{display:block}
.mxm-counter{position:absolute;top:12px;right:14px;color:#fff;font-size:13px;background:rgba(0,0,0,.55);padding:3px 10px;border-radius:20px}
.mxm-nav{position:absolute;top:50%;transform:translateY(-50%);background:rgba(0,0,0,.55);border:none;color:#fff;width:42px;height:42px;border-radius:50%;cursor:pointer;font-size:22px;display:flex;align-items:center;justify-content:center;transition:background .2s;z-index:5}
.mxm-nav:hover{background:rgba(0,0,0,.9)}
.mxm-nav.lft{left:14px}.mxm-nav.rgt{right:14px}
.mxm-hint{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,.65);color:#fff;padding:10px 20px;border-radius:24px;font-size:13px;pointer-events:none;white-space:nowrap;transition:opacity .5s}
.mxm-sidebar{width:140px;background:#111;overflow-y:auto;display:flex;flex-direction:column;gap:6px;padding:8px;flex-shrink:0;scrollbar-width:thin;scrollbar-color:#444 #111}
.mxm-sidebar::-webkit-scrollbar{width:3px}
.mxm-thumb{border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border-color .2s;flex-shrink:0}
.mxm-thumb img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
.mxm-thumb.active{border-color:#5b9bf8}
.mxm-color-wrap{flex:1;overflow-y:auto;background:#111;padding:16px 20px}
.mxm-swatches{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px}
.mxm-swatch-item{display:flex;flex-direction:column;align-items:center;gap:5px;cursor:pointer}
.mxm-swatch{width:38px;height:38px;border-radius:50%;border:3px solid transparent;transition:border-color .15s}
.mxm-swatch.active{border-color:#5b9bf8}
.mxm-swatch-name{color:#999;font-size:11px;text-align:center;max-width:60px}
.mxm-color-imgs{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px}
.mxm-color-img{border-radius:8px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border-color .15s}
.mxm-color-img img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
.mxm-color-img.active{border-color:#5b9bf8}
.mxm-empty{color:#666;font-size:13px;text-align:center;padding:40px 0}
.mxm-stage.drag-mode{cursor:grab}
.mxm-stage.drag-mode:active{cursor:grabbing}
.mxc-view-btn{display:inline-flex;align-items:center;gap:7px;background:rgba(0,0,0,0.65);border:1px solid rgba(255,255,255,0.25);color:#fff;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer;transition:background .2s;text-decoration:none}
.mxc-view-btn:hover{background:rgba(0,0,0,0.9);color:#fff}
.mxc-view-btn svg{flex-shrink:0}
</style>

@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.breadcrumbs'), compact('car'))

<div class="container-fluid px-3 px-lg-4 mt-3">
<div class="row g-3 align-items-start">

{{-- ── LEFT col ── --}}
<div class="col-lg-8">

{{-- Gallery (unchanged) --}}
<div class="container-banner-activities car-detail-galleries car-detail-modern__gallery-wrap">
<div class="box-banner-activities car-detail-modern__gallery-main">
<div class="banner-activities-detail" style="position:relative;">
@foreach($images as $image)
<div class="banner-slide-activity" style="position:relative;">
<div class="wrapper-image">
{{ RvMedia::image($image, $car->name, 'large-rectangle') }}
</div>
@if($loop->first && $car->amenities->isNotEmpty())
<div style="position:absolute;bottom:0;left:0;right:1%;background:linear-gradient(transparent,rgba(0,0,0,0.88));padding:40px 24px 56px;pointer-events:none;z-index:5;">
<p style="color:rgba(255,255,255,0.6);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:10px;border-left:3px solid #B03A2E;padding-left:8px;">Top Features</p>
<div style="display:grid;grid-template-columns:repeat(2,minmax(0,180px));gap:8px;">
@foreach($car->amenities->take(4) as $amenity)
<div style="display:flex;align-items:center;gap:8px;background:rgba(0,0,0,0.45);border:1px solid rgba(255,255,255,0.12);border-radius:8px;padding:8px 12px;">
<span style="width:28px;height:28px;background:rgba(176,58,46,0.35);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
@if($amenity->icon)
{!! BaseHelper::renderIcon($amenity->icon, attributes: ['style' => 'width:14px;height:14px;color:#fff']) !!}
@else
{!! BaseHelper::renderIcon('ti ti-check', attributes: ['style' => 'width:14px;height:14px;color:#fff']) !!}
@endif
</span>
<span style="color:#fff;font-size:12px;font-weight:500;">{{ $amenity->name }}</span>
</div>
@endforeach
</div>
</div>
@endif
</div>
@endforeach

@if($youtubeId)
<div class="banner-slide-activity" style="position:relative;overflow:hidden;">
<div class="wrapper-image" style="position:relative;overflow:hidden;border-radius:20px;cursor:pointer;" onclick="mxOpenModal('videos')">
<img src="https://img.youtube.com/vi/{{ $youtubeId }}/maxresdefault.jpg" alt="Video Preview" style="width:100%;height:100%;object-fit:cover;display:block;" />
<div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;z-index:5;">
<div style="width:64px;height:64px;background:#B03A2E;border-radius:50%;display:flex;align-items:center;justify-content:center;">
<svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
</div>
</div>
</div>
</div>
@endif
</div>

{{-- "View All Photos" button overlay (bottom-left of gallery) --}}
<div class="d-none d-sm-block" style="position:absolute;bottom:14px;left:14px;z-index:20;">
<button class="mxc-view-btn" onclick="mxOpenModal('all')">
<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
{{ __('View All Photos') }}
</button>
</div>

<div class="d-none d-sm-block">
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.gallery-buttons'), ['car' => $car])
</div>
</div>

<div class="slider-thumnail-activities car-detail-modern__gallery-thumbs">
<div class="slider-nav-thumbnails-activities-detail">
@foreach($images as $image)
<div class="banner-slide">
{{ RvMedia::image($image, $car->name, 'medium-rectangle') }}
</div>
@endforeach

@if($youtubeId)
<div class="banner-slide video-thumb-slide" data-youtube="{{ $youtubeId }}" style="cursor:pointer;position:relative;overflow:hidden;border-radius:10px;">
<img src="https://img.youtube.com/vi/{{ $youtubeId }}/mqdefault.jpg" alt="Video" style="width:100%;height:100%;object-fit:cover;display:block;" />
<div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;z-index:10;">
<div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
<div style="width:32px;height:32px;background:#B03A2E;border-radius:50%;display:flex;align-items:center;justify-content:center;">
<svg width="12" height="12" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
</div>
<span style="color:#fff;font-size:11px;font-weight:600;">Video</span>
</div>
</div>
</div>
@endif
</div>
</div>

<div class="d-block d-sm-none">
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.gallery-buttons'), ['car' => $car, 'renderLightboxLinks' => false])
</div>
</div>

{{-- Header Card --}}
<div class="tour-header car-detail-modern__header">
@if ($car->reviews_count)
<div class="tour-rate mb-2"><div class="rate-element">@include(Theme::getThemeNamespace('views.car-rentals.rating'), ['car' => $car])</div></div>
@endif
<div class="tour-title-main car-detail-modern__title-wrap">
<p class="car-detail-modern__eyebrow mb-1">{{ __('Featured vehicle') }}</p>
<h4 class="neutral-1000 mb-2">{{ $car->name }}</h4>
<div class="car-detail-modern__quick-meta">
@if($car->year)<span>{{ $car->year }}</span>@endif
@if($car->transmission)<span>{!! BaseHelper::clean($car->transmission->name) !!}</span>@endif
@if($car->fuel)<span>{!! BaseHelper::clean($car->fuel->name) !!}</span>@endif
</div>
</div>
<div class="tour-metas car-detail-modern__meta-row d-flex align-items-center justify-content-between flex-wrap mt-3">
<div class="tour-meta-left d-flex align-items-center gap-3 flex-wrap">
@if ($car->current_location)
<p class="text-md-medium neutral-1000 mb-0 tour-location d-flex align-items-center gap-1">{!! BaseHelper::renderIcon('ti ti-map-pin') !!}{!! BaseHelper::clean($car->current_location) !!}</p>
<a class="text-md-medium neutral-1000" href="https://maps.google.com/maps?q={{ addslashes($car->current_location) }}">{{ __('Show on map') }}</a>
@endif
</div>
<div>@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.share-button'), compact('car'))</div>
</div>
</div>

{{-- Details --}}
<div class="car-detail-modern__layout">
<div class="col-lg-12">
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.attributes'), compact('car'))
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.additional-info'), compact('car'))
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.amenities'), compact('car'))
<div class="box-collapse-expand">
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.content'), compact('car'))
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.owner-info'), compact('car'))
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.faqs'), compact('car'))
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.reviews'), compact('car', 'reviews'))
</div>
</div>
</div>

</div>

<div class="col-lg-4">
<div class="car-detail-modern__form-wrap">
@if($car->is_for_sale && get_car_rentals_setting('enabled_car_sale', true))
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.sale-info'), compact('car'))
@elseif(!$car->is_for_sale && CarRentalsHelper::isRentalBookingEnabled())
@include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.booking-form'), compact('car'))
@endif
@include(Theme::getThemeNamespace('views.car-rentals.message-form'), compact('car'))
</div>
</div>

</div>
</div>

<div id="mxPhotoModal">
<div class="mxm-head">
<span class="mxm-title">{{ $car->name }}</span>
<button class="mxm-close" onclick="mxCloseModal()">&#10005;</button>
</div>

<div class="mxm-tabs" id="mxTabs">
<button class="mxm-tab active" data-tab="all" onclick="mxSwitchTab('all')">
All Photos
</button>
@if(!empty($img360))
<button class="mxm-tab" data-tab="360" onclick="mxSwitchTab('360')">
<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="10" ry="4"/><path d="M2 12a10 10 0 0 0 20 0"/></svg>
360°
</button>
@endif
@if(!empty($imgExterior))
<button class="mxm-tab" data-tab="exterior" onclick="mxSwitchTab('exterior')">Exterior</button>
@endif
@if(!empty($imgInterior))
<button class="mxm-tab" data-tab="interior" onclick="mxSwitchTab('interior')">Interior</button>
@endif
@if(!empty($colorGroups))
<button class="mxm-tab" data-tab="colors" onclick="mxSwitchTab('colors')">Colors</button>
@endif
@if($youtubeId)
<button class="mxm-tab" data-tab="videos" onclick="mxSwitchTab('videos')">Videos</button>
@endif
</div>

<div class="mxm-body" id="mxBody">

{{-- Stage + Sidebar (for non-color, non-video tabs) --}}
<div id="mxGalleryView" style="display:flex;flex:1;overflow:hidden;">
<div class="mxm-stage" id="mxStage">
<div class="mxm-counter" id="mxCounter"></div>
<div class="mxm-hint" id="mxHint" style="opacity:0">&#8596; Drag to rotate</div>
<button class="mxm-nav lft" id="mxPrev">&#8249;</button>
<button class="mxm-nav rgt" id="mxNext">&#8250;</button>
</div>
<div class="mxm-sidebar" id="mxSidebar"></div>
</div>

<div id="mxColorsView" class="mxm-color-wrap" style="display:none;flex:1;overflow-y:auto;">
<div class="mxm-swatches" id="mxSwatches"></div>
<div style="color:#ccc;font-size:14px;font-weight:600;margin-bottom:12px;" id="mxColorName"></div>
<div class="mxm-color-imgs" id="mxColorImgs"></div>
</div>

</div>
</div>

</div>

@php
$mxData = [
'carName' => $car->name,
'youtubeId' => $youtubeId,
'imgAll' => array_values(array_map(fn($i) => RvMedia::url($i), $imgAll)),
'imgExterior' => array_values(array_map(fn($i) => RvMedia::url($i), $imgExterior)),
'imgInterior' => array_values(array_map(fn($i) => RvMedia::url($i), $imgInterior)),
'img360' => array_values(array_map(fn($i) => RvMedia::url($i), $img360)),
'colorGroups' => array_map(
fn($grp) => array_values(array_map(fn($i) => RvMedia::url($i), $grp)),
$colorGroups
),
];
@endphp
<script>
var MX_DATA = {!! json_encode($mxData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
</script>

<script>
(function(){
var modal = document.getElementById('mxPhotoModal');
var stage = document.getElementById('mxStage');
var sidebar = document.getElementById('mxSidebar');
var counter = document.getElementById('mxCounter');
var hint = document.getElementById('mxHint');
var gallView= document.getElementById('mxGalleryView');
var clrView = document.getElementById('mxColorsView');

var curIdx = 0;
var curImgs = [];
var is360 = false;
var frame = 0;
var dragging= false;
var dragX = 0;
var activeSwatch = 0;

window.mxOpenModal = function(tab) {
modal.classList.add('open');
document.body.style.overflow = 'hidden';
mxSwitchTab(tab || 'all');
};
window.mxCloseModal = function() {
modal.classList.remove('open');
document.body.style.overflow = '';
clearStage();
};

window.mxSwitchTab = function(tab) {
document.querySelectorAll('.mxm-tab').forEach(function(t){
t.classList.toggle('active', t.dataset.tab === tab);
});
is360 = false;
stage.classList.remove('drag-mode');
hint.style.opacity = '0';

if (tab === 'colors') {
gallView.style.display = 'none';
clrView.style.display = 'block';
buildColorPanel(0);
return;
}
if (tab === 'videos') {
gallView.style.display = 'flex';
clrView.style.display = 'none';
clearStage();
var iframe = document.createElement('iframe');
iframe.src = 'https://www.youtube.com/embed/' + MX_DATA.youtubeId + '?autoplay=1';
iframe.className = 'show';
iframe.allow = 'autoplay;encrypted-media;fullscreen';
stage.appendChild(iframe);
sidebar.innerHTML = '';
counter.textContent = 'Video';
document.getElementById('mxPrev').style.display = 'none';
document.getElementById('mxNext').style.display = 'none';
return;
}

gallView.style.display = 'flex';
clrView.style.display = 'none';
document.getElementById('mxPrev').style.display = '';
document.getElementById('mxNext').style.display = '';

var imgs = tab === '360' ? MX_DATA.img360
: tab === 'exterior' ? MX_DATA.imgExterior
: tab === 'interior' ? MX_DATA.imgInterior
: MX_DATA.imgAll;

if (!imgs || imgs.length === 0) {
clearStage();
stage.innerHTML += '<div class="mxm-empty">No images in this category</div>';
return;
}

if (tab === '360') {
load360(imgs);
} else {
loadImages(imgs);
}
};

function clearStage() {
stage.querySelectorAll('img.mx-main, iframe').forEach(function(e){e.remove()});
var empty = stage.querySelector('.mxm-empty');
if (empty) empty.remove();
}

function loadImages(imgs) {
clearStage();
curImgs = imgs;
imgs.forEach(function(src, i) {
var img = document.createElement('img');
img.className = 'mx-main' + (i === 0 ? ' show' : '');
img.src = src;
stage.appendChild(img);
});
buildSidebar(imgs);
curIdx = 0;
updateCounter();
}

function buildSidebar(imgs) {
sidebar.innerHTML = '';
imgs.forEach(function(src, i) {
var d = document.createElement('div');
d.className = 'mxm-thumb' + (i === 0 ? ' active' : '');
var img = document.createElement('img');
img.src = src;
d.appendChild(img);
d.onclick = function(){goTo(i)};
sidebar.appendChild(d);
});
}

function goTo(idx) {
curIdx = idx;
stage.querySelectorAll('img.mx-main').forEach(function(el, i){
el.classList.toggle('show', i === idx);
});
sidebar.querySelectorAll('.mxm-thumb').forEach(function(t, i){
t.classList.toggle('active', i === idx);
if (i === idx) t.scrollIntoView({block:'nearest'});
});
updateCounter();
}

function updateCounter() {
counter.textContent = (curIdx + 1) + ' of ' + curImgs.length;
}

document.getElementById('mxPrev').onclick = function(){
if (is360) return;
goTo((curIdx - 1 + curImgs.length) % curImgs.length);
};
document.getElementById('mxNext').onclick = function(){
if (is360) return;
goTo((curIdx + 1) % curImgs.length);
};

function load360(imgs) {
clearStage();
is360 = true;
frame = 0;
curImgs = imgs;
stage.classList.add('drag-mode');
imgs.forEach(function(src, i) {
var img = document.createElement('img');
img.className = 'mx-main' + (i === 0 ? ' show' : '');
img.src = src;
stage.appendChild(img);
});
buildSidebar(imgs);
hint.style.opacity = '1';
counter.textContent = '360°';
document.getElementById('mxPrev').style.display = 'none';
document.getElementById('mxNext').style.display = 'none';
setTimeout(function(){hint.style.opacity='0';}, 3000);
}

function set360Frame(f) {
var total = curImgs.length;
frame = ((f % total) + total) % total;
stage.querySelectorAll('img.mx-main').forEach(function(el, i){
el.classList.toggle('show', i === frame);
});
counter.textContent = '360° — ' + Math.round((frame / total) * 360) + '°';
}

stage.addEventListener('mousedown', function(e){if(is360){dragging=true;dragX=e.clientX;}});
window.addEventListener('mousemove', function(e){
if(!dragging||!is360)return;
var d=e.clientX-dragX;
if(Math.abs(d)>16){set360Frame(frame+(d>0?-1:1));dragX=e.clientX;}
});
window.addEventListener('mouseup', function(){dragging=false;});
stage.addEventListener('touchstart',function(e){if(is360){dragging=true;dragX=e.touches[0].clientX;}},{passive:true});
stage.addEventListener('touchmove',function(e){
if(!dragging||!is360)return;
var d=e.touches[0].clientX-dragX;
if(Math.abs(d)>14){set360Frame(frame+(d>0?-1:1));dragX=e.touches[0].clientX;}
},{passive:true});
stage.addEventListener('touchend',function(){dragging=false;});

function buildColorPanel(swatchIdx) {
activeSwatch = swatchIdx;
var keys = Object.keys(MX_DATA.colorGroups);
var swatchesEl = document.getElementById('mxSwatches');
var colorName = document.getElementById('mxColorName');
var colorImgs = document.getElementById('mxColorImgs');

swatchesEl.innerHTML = '';
keys.forEach(function(key, i) {
var wrap = document.createElement('div');
wrap.className = 'mxm-swatch-item';
var sw = document.createElement('div');
sw.className = 'mxm-swatch' + (i === activeSwatch ? ' active' : '');
sw.style.background = colorHex(key);
if (key === 'white' || key === 'silver') sw.style.border = '3px solid #555';
sw.onclick = function(){buildColorPanel(i);};
var lbl = document.createElement('div');
lbl.className = 'mxm-swatch-name';
lbl.textContent = key.charAt(0).toUpperCase() + key.slice(1);
wrap.appendChild(sw); wrap.appendChild(lbl);
swatchesEl.appendChild(wrap);
});

var activeKey = keys[activeSwatch];
colorName.textContent = activeKey ? (activeKey.charAt(0).toUpperCase() + activeKey.slice(1)) : '';
colorImgs.innerHTML = '';
(MX_DATA.colorGroups[activeKey] || []).forEach(function(src, i) {
var d = document.createElement('div');
d.className = 'mxm-color-img' + (i === 0 ? ' active' : '');
var img = document.createElement('img');
img.src = src;
d.appendChild(img);
colorImgs.appendChild(d);
});
}

function colorHex(name) {
var map = {
red:'#c0392b', black:'#111', white:'#f5f5f5', silver:'#c0c0c0',
blue:'#2980b9', green:'#27ae60', gray:'#7f8c8d', grey:'#7f8c8d',
brown:'#7B3F00', orange:'#e67e22', yellow:'#f1c40f', purple:'#8e44ad',
pearl:'#f0ece4', gold:'#d4af37'
};
return map[name.toLowerCase()] || '#888';
}

modal.addEventListener('click', function(e){if(e.target===modal)mxCloseModal();});

document.addEventListener('keydown', function(e){
if (!modal.classList.contains('open')) return;
if (e.key === 'Escape') mxCloseModal();
if (e.key === 'ArrowLeft' && !is360) goTo((curIdx-1+curImgs.length)%curImgs.length);
if (e.key === 'ArrowRight' && !is360) goTo((curIdx+1)%curImgs.length);
});

document.addEventListener('DOMContentLoaded', function(){
document.querySelectorAll('.video-thumb-slide').forEach(function(el){
el.addEventListener('click', function(e){
e.preventDefault(); e.stopPropagation();
mxOpenModal('videos');
});
});
});

})();
</script>
