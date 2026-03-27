@php
    Theme::set('pageTitle', $carTag->name);
@endphp

<div class="container">
    <h1>{{ $carTag->name }}</h1>

    @if($carTag->description)
        <p>{{ $carTag->description }}</p>
    @endif

    <div class="row">
        @forelse($cars as $car)
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if($car->image)
                        <img src="{{ RvMedia::getImageUrl($car->image) }}" class="card-img-top" alt="{{ $car->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $car->name }}</h5>
                        <a href="{{ $car->url }}" class="btn btn-primary">{{ __('View Details') }}</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>{{ __('No cars found') }}</p>
            </div>
        @endforelse
    </div>

    {!! $cars->withQueryString()->links() !!}
</div>
