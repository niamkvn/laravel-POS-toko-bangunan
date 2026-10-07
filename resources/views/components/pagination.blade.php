@props(['paginator'])
@if($paginator->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="small text-muted">
        Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
    </div>
    <div>
        {{ $paginator->onEachSide(1)->links() }}
    </div>
</div>
@endif
