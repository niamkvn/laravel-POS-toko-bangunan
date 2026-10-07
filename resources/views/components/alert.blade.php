@props(['type' => 'soft', 'dismissible' => false])
<div {{ $attributes->merge(['class' => 'nb-card-flat p-3 d-flex align-items-start gap-2 stat-card variant-'.$type]) }} @if($dismissible) x-data="{ show: true }" x-show="show" @endif>
    <i class="bi bi-info-circle-fill mt-1"></i>
    <div class="flex-grow-1">{{ $slot }}</div>
    @if($dismissible)
        <button type="button" class="btn-close" @click="show = false"></button>
    @endif
</div>
