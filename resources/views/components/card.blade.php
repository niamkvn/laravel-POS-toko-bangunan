@props(['title' => null, 'subtitle' => null])
<div {{ $attributes->merge(['class' => 'nb-card']) }}>
    @if($title || isset($actions))
        <div class="nb-panel-header">
            <div>
                @if($title)<h6 class="mb-0">{{ $title }}</h6>@endif
                @if($subtitle)<p class="small text-muted mb-0">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)<div>{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="nb-panel-body">
        {{ $slot }}
    </div>
</div>
