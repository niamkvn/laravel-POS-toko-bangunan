@props(['icon' => 'bi-inbox', 'title' => 'Belum ada data', 'description' => null, 'actionLabel' => null, 'actionHref' => null])
<div class="empty-state">
    <div class="empty-icon"><i class="bi {{ $icon }}"></i></div>
    <h6 class="mb-1">{{ $title }}</h6>
    @if($description)<p class="text-muted small mb-3">{{ $description }}</p>@endif
    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> {{ $actionLabel }}</a>
    @endif
</div>
