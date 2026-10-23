<x-app-layout :title="$supplier->exists ? 'Edit Supplier' : 'Tambah Supplier'">
    <x-card :title="$supplier->exists ? 'Edit Supplier' : 'Tambah Supplier'">
        <form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
            @csrf
            @if($supplier->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6"><x-input label="Nama" name="name" :value="$supplier->name" required /></div>
                <div class="col-md-6"><x-input label="Nama Perusahaan" name="company_name" :value="$supplier->company_name" /></div>
                <div class="col-md-6"><x-input label="Telepon" name="phone" :value="$supplier->phone" /></div>
                <div class="col-md-6"><x-input type="email" label="Email" name="email" :value="$supplier->email" /></div>
                <div class="col-md-12"><x-input label="Alamat" name="address" :value="$supplier->address" /></div>
                <div class="col-md-4"><x-input label="NPWP" name="tax_number" :value="$supplier->tax_number" /></div>
                <div class="col-md-4"><x-input type="number" label="Termin (hari)" name="payment_term_days" :value="$supplier->payment_term_days ?? 0" required /></div>
                <div class="col-md-4"><x-input type="number" step="0.01" label="Limit Kredit" name="credit_limit" :value="$supplier->credit_limit ?? 0" required /></div>
                <div class="col-md-12"><x-input label="Catatan" name="notes" :value="$supplier->notes" /></div>
            </div>
            <div class="form-check my-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($supplier->exists ? $supplier->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
