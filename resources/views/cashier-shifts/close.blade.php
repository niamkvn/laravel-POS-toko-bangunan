<x-app-layout title="Tutup Shift Kasir">
    <x-card title="Tutup Shift Kasir" style="max-width:480px;">
        <div class="mb-3">
            <div class="d-flex justify-content-between py-1"><span>Saldo Awal</span><strong>Rp {{ number_format($shift->opening_balance,0,',','.') }}</strong></div>
            <div class="d-flex justify-content-between py-1"><span>Penjualan Tunai</span><strong>Rp {{ number_format($shift->cash_sales,0,',','.') }}</strong></div>
            <div class="d-flex justify-content-between py-1"><span>Kas Masuk</span><strong>Rp {{ number_format($shift->cash_in,0,',','.') }}</strong></div>
            <div class="d-flex justify-content-between py-1"><span>Kas Keluar</span><strong>Rp {{ number_format($shift->cash_out,0,',','.') }}</strong></div>
            <hr>
            <div class="d-flex justify-content-between py-1"><span class="fw-bold">Kas Diharapkan</span><strong>Rp {{ number_format($shift->expected_cash,0,',','.') }}</strong></div>
        </div>
        <form method="POST" action="{{ route('cashier-shifts.close') }}">
            @csrf
            <div class="mb-3"><x-input type="number" step="0.01" label="Kas Aktual (Fisik)" name="actual_cash" :value="$shift->expected_cash" required /></div>
            <div class="mb-3"><x-input label="Catatan" name="notes" /></div>
            <button class="btn btn-deep btn-nb w-100">Tutup Shift</button>
        </form>
    </x-card>
</x-app-layout>
