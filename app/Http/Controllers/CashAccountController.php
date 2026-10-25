<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use Illuminate\Http\Request;

class CashAccountController extends Controller
{
    public function index()
    {
        $cashAccounts = CashAccount::orderBy('name')->paginate(15);

        return view('cash-accounts.index', compact('cashAccounts'));
    }

    public function create()
    {
        return view('cash-accounts.form', ['cashAccount' => new CashAccount]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['current_balance'] = $data['opening_balance'];
        CashAccount::create($data);

        return redirect()->route('cash-accounts.index')->with('success', 'Kas / Bank berhasil ditambahkan.');
    }

    public function edit(CashAccount $cashAccount)
    {
        return view('cash-accounts.form', compact('cashAccount'));
    }

    public function update(Request $request, CashAccount $cashAccount)
    {
        $data = $this->validated($request, $cashAccount->id);
        unset($data['opening_balance']);
        $cashAccount->update($data);

        return redirect()->route('cash-accounts.index')->with('success', 'Kas / Bank berhasil diperbarui.');
    }

    public function destroy(CashAccount $cashAccount)
    {
        if ($cashAccount->transactions()->exists()) {
            return back()->with('error', 'Kas / Bank tidak dapat dihapus karena memiliki riwayat transaksi.');
        }
        $cashAccount->delete();

        return back()->with('success', 'Kas / Bank berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:cash_accounts,code'.($id ? ",{$id}" : ''),
            'name' => 'required|string|max:150',
            'type' => 'required|in:cash,bank,ewallet',
            'account_number' => 'nullable|string|max:50',
            'opening_balance' => 'required|numeric|min:0',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
