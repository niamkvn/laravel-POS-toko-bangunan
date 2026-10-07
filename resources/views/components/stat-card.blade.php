@props(['label', 'value', 'icon' => 'bi-graph-up', 'variant' => 'primary'])
<div class="stat-card variant-{{ $variant }} h-100 d-flex flex-column justify-content-between" style="min-width:0;max-width:100%;overflow:hidden;">
    <div class="d-flex justify-content-between align-items-start" style="min-width:0;">
        <div style="min-width:0;flex:1 1 auto;">
            <div class="stat-label">{{ $label }}</div>
            <div class="stat-value">{{ $value }}</div>
        </div>
        <i class="bi {{ $icon }}" style="font-size:1.4rem;opacity:.6;flex-shrink:0;margin-left:.5rem;"></i>
    </div>
    @isset($footer)
        <div class="small mt-2" style="opacity:.75;">{{ $footer }}</div>
    @endisset
</div>
