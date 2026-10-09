<x-app-layout :title="$paymentMethod->exists ? 'Edit Payment Method' : 'Tambah Payment Method'">
    <x-card :title="$paymentMethod->exists ? 'Edit Payment Method' : 'Tambah Payment Method'" style="max-width:480px;">
        <form method="POST" action="{{ $paymentMethod->exists ? route('payment-methods.update', $paymentMethod) : route('payment-methods.store') }}">
            @csrf
            @if($paymentMethod->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Kode" name="code" :value="$paymentMethod->code" required /></div>
            <div class="mb-3"><x-input label="Nama" name="name" :value="$paymentMethod->name" required /></div>
            <div class="mb-3">
                <x-select label="Tipe" name="type" :options="['cash'=>'Cash','bank_transfer'=>'Bank Transfer','qris'=>'QRIS','ewallet'=>'E-Wallet','credit'=>'Credit','other'=>'Other']" :selected="$paymentMethod->type" required />
            </div>
            <div class="mb-3"><x-input label="No. Rekening/Akun" name="account_number" :value="$paymentMethod->account_number" /></div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($paymentMethod->exists ? $paymentMethod->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('payment-methods.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
