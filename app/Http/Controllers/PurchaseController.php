<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $purchaseService) {}

    public function index(Request $request)
    {
        $purchases = Purchase::with('supplier')
            ->when($request->search, fn ($q) => $q->where('purchase_number', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('purchase_date')->paginate(15)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'items.unit', 'payments.cashAccount', 'supplier', 'warehouse', 'returns');

        return view('purchases.show', compact('purchase'));
    }

    public function pay(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'reference_number' => 'nullable|string|max:100',
        ]);

        try {
            $this->purchaseService->payPurchase($purchase, $data['amount'], $data['cash_account_id'], $data['reference_number'] ?? null);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('success', 'Pembayaran hutang berhasil dicatat.');
    }
}
