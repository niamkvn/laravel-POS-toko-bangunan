<x-app-layout :title="$cashAccount->exists ? 'Edit Kas' : 'Tambah Kas'">
    <x-card :title="$cashAccount->exists ? 'Edit Kas' : 'Tambah Kas'" style="max-width:560px;">
        <form method="POST" action="{{ $cashAccount->exists ? route('cash-accounts.update', $cashAccount) : route('cash-accounts.store') }}">
            @csrf
            @if($cashAccount->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Kode" name="code" :value="$cashAccount->code" required /></div>
            <div class="mb-3"><x-input label="Nama" name="name" :value="$cashAccount->name" required /></div>
            <div class="mb-3"><x-select label="Tipe" name="type" :options="['cash'=>'Cash','bank'=>'Bank','ewallet'=>'E-Wallet']" :selected="$cashAccount->type" required /></div>
            <div class="mb-3"><x-input label="No. Rekening" name="account_number" :value="$cashAccount->account_number" /></div>
            @if(!$cashAccount->exists)
            <div class="mb-3"><x-input type="number" step="0.01" label="Saldo Awal" name="opening_balance" value="0" required /></div>
            @endif
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($cashAccount->exists ? $cashAccount->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <button class="btn btn-primary btn-nb">Simpan</button>
            <a href="{{ route('cash-accounts.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </form>
    </x-card>
</x-app-layout>
