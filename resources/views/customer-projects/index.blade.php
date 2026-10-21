<x-app-layout title="Project">
    <x-card>
        <x-slot:actions>@can('project.create')<a href="{{ route('customer-projects.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Project</a>@endcan</x-slot:actions>
        @if($projects->isEmpty())
            <x-empty-state title="Belum ada project" action-label="Tambah Project" :action-href="route('customer-projects.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama Project</th><th>Customer</th><th>Lokasi</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($projects as $p)
                <tr>
                    <td class="fw-bold">{{ $p->project_code }}</td>
                    <td><a href="{{ route('customer-projects.show', $p) }}">{{ $p->name }}</a></td>
                    <td>{{ $p->customer->name ?? '-' }}</td>
                    <td>{{ $p->location }}</td>
                    <td><x-badge :variant="$p->status==='completed'?'mint':($p->status==='cancelled'?'danger':'soft')">{{ ucfirst($p->status) }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('customer-projects.edit', $p) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$projects" />
        @endif
    </x-card>
</x-app-layout>
