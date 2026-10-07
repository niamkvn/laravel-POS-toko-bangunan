@props(['variant' => 'soft'])
@php
$colors = [
    'soft' => 'background:var(--color-secondary);',
    'mint' => 'background:var(--color-mint);',
    'warning' => 'background:var(--color-warning);',
    'danger' => 'background:var(--color-danger);',
    'deep' => 'background:var(--color-deep);color:#fff;',
    'white' => 'background:#fff;',
];
@endphp
<span class="badge-nb" style="{{ $colors[$variant] ?? $colors['soft'] }}">{{ $slot }}</span>
