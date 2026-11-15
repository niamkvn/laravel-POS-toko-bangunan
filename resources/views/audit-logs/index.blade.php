<x-app-layout title="Audit Log">
    <x-card title="Audit Log">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <x-select label="Modul" name="module" :options="$modules->mapWithKeys(fn($m) => [$m => ucfirst($m)])" :selected="request('module')" placeholder="Semua Modul" auto-submit />
            </div>
            <div class="col-md-6"><x-date-filter :from="request('date_from')" :to="request('date_to')" /></div>
            <div class="col-md-2"><x-field-button><button class="btn btn-outline btn-nb w-100">Filter</button></x-field-button></div>
        </form>

        @if($logs->isEmpty())
            <x-empty-state title="Belum ada aktivitas" />
        @else
        <x-table>
            <thead><tr><th>Waktu</th><th>User</th><th>Modul</th><th>Aksi</th><th>Record</th><th>IP</th></tr></thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td class="small">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td class="small">{{ $log->user->name ?? 'System' }}</td>
                    <td><x-badge>{{ $log->module }}</x-badge></td>
                    <td class="small">{{ $log->action }}</td>
                    <td class="small">{{ class_basename($log->record_type ?? '') }} #{{ $log->record_id }}</td>
                    <td class="small text-muted">{{ $log->ip_address }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$logs" />
        @endif
    </x-card>
</x-app-layout>
