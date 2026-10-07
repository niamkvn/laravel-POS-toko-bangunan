@props(['id', 'title' => null, 'size' => ''])
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $size }}">
        <div class="modal-content" style="border:3px solid var(--color-border);border-radius:8px;box-shadow:5px 5px 0 var(--color-border);">
            @if($title)
            <div class="modal-header" style="border-bottom:3px solid var(--color-border);">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @endif
            <div class="modal-body">
                {{ $slot }}
            </div>
            @isset($footer)
            <div class="modal-footer" style="border-top:3px solid var(--color-border);">
                {{ $footer }}
            </div>
            @endisset
        </div>
    </div>
</div>
