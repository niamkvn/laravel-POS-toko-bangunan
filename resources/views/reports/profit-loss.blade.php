<x-app-layout title="Laporan Laba Rugi">
    <x-card title="Laba Rugi">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5"><x-date-filter :from="$from" :to="$to" /></div>
            <div class="col-md-2"><x-field-button><button class="btn btn-outline btn-nb w-100">Filter</button></x-field-button></div>
        </form>
        <table class="table">
            <tr><td>Pendapatan (Revenue)</td><td class="text-end fw-bold">Rp {{ number_format($revenue,0,',','.') }}</td></tr>
            <tr><td>Harga Pokok Penjualan (COGS)</td><td class="text-end">(Rp {{ number_format($cogs,0,',','.') }})</td></tr>
            <tr style="border-top:2px solid var(--color-border);"><td class="fw-bold">Laba Kotor</td><td class="text-end fw-bold">Rp {{ number_format($grossProfit,0,',','.') }}</td></tr>
            <tr><td>Beban Operasional (Expense)</td><td class="text-end">(Rp {{ number_format($expenses,0,',','.') }})</td></tr>
            <tr style="border-top:3px solid var(--color-border);"><td class="fw-bold fs-5">Laba Bersih</td><td class="text-end fw-bold fs-5">Rp {{ number_format($netProfit,0,',','.') }}</td></tr>
        </table>
    </x-card>
</x-app-layout>
