@if($content = $car->content)
    <div class="group-collapse-expand group-collapse-expand--modern">
        <button class="btn btn-collapse car-detail-modern__collapse-trigger" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOverview" aria-expanded="true" aria-controls="collapseOverview">
            <strong class="heading-6">{{ __('Overview') }}</strong>
            <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L6 6L11 1" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>

        @if($content = $car->content)
            <div class="collapse show" id="collapseOverview">
                <div class="card card-body ck-content car-detail-modern__collapse-card">
                    {!! BaseHelper::clean($content) !!}
                </div>
            </div>
        @endif
    </div>
@endif
