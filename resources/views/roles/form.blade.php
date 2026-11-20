<x-app-layout :title="$role->exists ? 'Edit Role' : 'Tambah Role'">
    <x-card :title="$role->exists ? 'Edit Role' : 'Tambah Role'">
        <form method="POST" action="{{ $role->exists ? route('roles.update', $role) : route('roles.store') }}">
            @csrf
            @if($role->exists) @method('PUT') @endif
            <div class="mb-4" style="max-width:400px;"><x-input label="Nama Role" name="name" :value="$role->name" required /></div>

            <h6 class="mb-3">Permissions</h6>
            <div class="row g-3">
                @foreach($permissionGroups as $group => $perms)
                <div class="col-md-4">
                    <div class="nb-card-flat p-3">
                        <div class="fw-bold small text-uppercase mb-2">{{ str_replace('_', ' ', $group) }}</div>
                        @foreach($perms as $perm)
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="form-check-input" id="perm-{{ $perm->id }}" @checked(in_array($perm->name, $rolePermissions))>
                            <label class="form-check-label small" for="perm-{{ $perm->id }}">{{ $perm->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-nb">Simpan Role</button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
