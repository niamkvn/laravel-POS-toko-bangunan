<x-app-layout :title="$user->exists ? 'Edit User' : 'Tambah User'">
    <x-card :title="$user->exists ? 'Edit User' : 'Tambah User'" style="max-width:600px;">
        <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if($user->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6"><x-input label="Kode Karyawan" name="employee_code" :value="$user->employee_code" /></div>
                <div class="col-md-6"><x-input label="Nama" name="name" :value="$user->name" required /></div>
                <div class="col-md-6"><x-input type="email" label="Email" name="email" :value="$user->email" required /></div>
                <div class="col-md-6"><x-input label="Telepon" name="phone" :value="$user->phone" /></div>
                <div class="col-md-6"><x-password label="Password" name="password" :required="!$user->exists" /></div>
                <div class="col-md-6"><x-select label="Role" name="role" :options="$roles" :selected="$user->roles->first()->name ?? null" required /></div>
            </div>
            <div class="form-check my-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($user->exists ? $user->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
