<x-app-layout title="Kasir Shift">
    <x-card title="Riwayat Shift Kasir">
        <x-table>
            <thead><tr><th>Kasir</th><th>Kas</th><th>Buka</th><th>Tutup</th><th>Expected</th><th>Actual</th><th>Selisih</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($shifts as $s)
                <tr>
                    <td><a href="{{ route('cashier-shifts.show', $s) }}">{{ $s->user->name ?? '-' }}</a></td>
                    <td>{{ $s->cashAccount->name ?? '-' }}</td>
                    <td class="small">{{ $s->opened_at->format('d/m/Y H:i') }}</td>
                    <td class="small">{{ $s->closed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>Rp {{ number_format($s->expected_cash,0,',','.') }}</td>
                    <td>Rp {{ number_format($s->actual_cash ?? 0,0,',','.') }}</td>
                    <td class="{{ ($s->difference ?? 0) < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                        {{ $s->difference !== null ? number_format($s->difference,0,',','.') : '-' }}
                    </td>
                    <td><x-badge :variant="$s->status==='open'?'mint':'soft'">{{ ucfirst($s->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$shifts" />
    </x-card>
</x-app-layout>
