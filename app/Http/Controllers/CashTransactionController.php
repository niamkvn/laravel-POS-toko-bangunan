<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = CashTransaction::with('cashAccount', 'user')
            ->when($request->cash_account_id, fn ($q) => $q->where('cash_account_id', $request->cash_account_id))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->date_from, fn ($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('transaction_date', '<=', $request->date_to))
            ->latest('transaction_date')->paginate(20)->withQueryString();

        $cashAccounts = CashAccount::pluck('name', 'id');

        return view('cash-transactions.index', compact('transactions', 'cashAccounts'));
    }

    public function create()
    {
        return view('cash-transactions.create', ['cashAccounts' => CashAccount::where('active', true)->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'nullable|string|max:100',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $account = CashAccount::findOrFail($data['cash_account_id']);

                if ($data['type'] === 'out' && $account->current_balance < $data['amount']) {
                    throw ValidationException::withMessages(['amount' => 'Saldo kas tidak mencukupi.']);
                }

                CashTransaction::create([
                    ...$data,
                    'user_id' => auth()->id(),
                ]);

                $account->increment('current_balance', $data['type'] === 'in' ? $data['amount'] : -$data['amount']);
            });
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('cash-transactions.index')->with('success', 'Mutasi kas berhasil dicatat.');
    }
}
