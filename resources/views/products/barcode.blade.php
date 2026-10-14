<x-app-layout title="Cetak Barcode">
    <x-card title="Cetak Barcode Produk">
        <form method="GET" class="mb-3" style="max-width:320px;"><x-search name="search" :value="request('search')" placeholder="Cari produk..." /></form>

        <form id="printForm">
            <div class="row g-3 mb-3">
                @foreach($products as $p)
                <div class="col-md-3 col-6">
                    <label class="nb-card-flat p-2 d-block text-center" style="cursor:pointer;">
                        <input type="checkbox" class="form-check-input mb-2 barcode-check" value="{{ $p->id }}">
                        <div class="small fw-bold">{{ $p->name }}</div>
                        <div class="small text-muted">{{ $p->barcode ?? $p->sku }}</div>
                        <div style="font-family: 'Libre Barcode 128', monospace; font-size:1.5rem; letter-spacing:1px;">*{{ $p->barcode ?? $p->sku }}*</div>
                    </label>
                </div>
                @endforeach
            </div>
        </form>
        <x-pagination :paginator="$products" />
        <button type="button" class="btn btn-primary btn-nb" onclick="window.print()"><i class="bi bi-printer"></i> Cetak Label Terpilih</button>
    </x-card>
</x-app-layout>
