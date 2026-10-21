<x-app-layout :title="$project->exists ? 'Edit Project' : 'Tambah Project'">
    <x-card :title="$project->exists ? 'Edit Project' : 'Tambah Project'">
        <form method="POST" action="{{ $project->exists ? route('customer-projects.update', $project) : route('customer-projects.store') }}">
            @csrf
            @if($project->exists) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6"><x-select label="Customer" name="customer_id" :options="$customers" :selected="$project->customer_id" required /></div>
                <div class="col-md-6"><x-input label="Nama Project" name="name" :value="$project->name" required /></div>
                <div class="col-md-6"><x-input label="Lokasi" name="location" :value="$project->location" /></div>
                <div class="col-md-6">
                    <x-select label="Status" name="status" :selected="$project->status ?? 'planning'" required
                        :options="['planning'=>'Planning','ongoing'=>'Ongoing','completed'=>'Completed','cancelled'=>'Cancelled']" />
                </div>
                <div class="col-md-4"><x-input type="date" label="Tanggal Mulai" name="start_date" :value="$project->start_date?->format('Y-m-d')" /></div>
                <div class="col-md-4"><x-input type="date" label="Tanggal Selesai" name="end_date" :value="$project->end_date?->format('Y-m-d')" /></div>
                <div class="col-md-4"><x-input type="number" step="0.01" label="Budget" name="budget" :value="$project->budget" /></div>
                <div class="col-md-12"><x-input label="Catatan" name="notes" :value="$project->notes" /></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('customer-projects.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
