<x-app-layout :title="$expense->expense_number">
    <x-card :title="$expense->expense_number">
        <p class="mb-1"><strong>Kategori:</strong> {{ $expense->category->name ?? '-' }}</p>
        <p class="mb-1"><strong>Kas / Bank:</strong> {{ $expense->cashAccount->name ?? '-' }}</p>
        <p class="mb-1"><strong>Tanggal:</strong> {{ $expense->expense_date->format('d/m/Y') }}</p>
        <p class="mb-1"><strong>Deskripsi:</strong> {{ $expense->description }}</p>
        <p class="mb-1"><strong>Dicatat oleh:</strong> {{ $expense->user->name ?? '-' }}</p>
        <h5 class="mt-3">Rp {{ number_format($expense->amount,0,',','.') }}</h5>
    </x-card>
</x-app-layout>
