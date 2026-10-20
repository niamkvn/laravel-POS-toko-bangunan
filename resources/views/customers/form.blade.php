<x-app-layout :title="$customer->exists ? 'Edit Customer' : 'Tambah Customer'">
    <x-card :title="$customer->exists ? 'Edit Customer' : 'Tambah Customer'">
        <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
            @csrf
            @if($customer->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6"><x-input label="Nama" name="name" :value="$customer->name" required /></div>
                <div class="col-md-6"><x-input label="Nama Perusahaan" name="company_name" :value="$customer->company_name" /></div>
                <div class="col-md-6"><x-input label="Telepon" name="phone" :value="$customer->phone" /></div>
                <div class="col-md-6"><x-input type="email" label="Email" name="email" :value="$customer->email" /></div>
                <div class="col-md-12"><x-input label="Alamat" name="address" :value="$customer->address" /></div>
                <div class="col-md-4">
                    <x-select label="Tipe Customer" name="customer_type" required :selected="$customer->customer_type"
                        :options="['umum'=>'Umum','tukang'=>'Tukang','reseller'=>'Reseller','kontraktor'=>'Kontraktor','developer'=>'Developer','perusahaan'=>'Perusahaan']" />
                </div>
                <div class="col-md-4"><x-select label="Price Level" name="price_level_id" :options="$priceLevels" :selected="$customer->price_level_id" placeholder="Default (Umum)" /></div>
                <div class="col-md-4"><x-input type="number" step="0.01" label="Limit Kredit" name="credit_limit" :value="$customer->credit_limit ?? 0" required /></div>
                <div class="col-md-4"><x-input type="number" label="Termin Pembayaran (hari)" name="payment_term_days" :value="$customer->payment_term_days ?? 0" required /></div>
                <div class="col-md-4"><x-input label="NPWP" name="tax_number" :value="$customer->tax_number" /></div>
                <div class="col-md-12"><x-input label="Catatan" name="notes" :value="$customer->notes" /></div>
            </div>
            <div class="form-check my-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($customer->exists ? $customer->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
