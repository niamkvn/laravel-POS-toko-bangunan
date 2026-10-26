<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = CashierShift::with('user', 'cashAccount')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('opened_at')->paginate(15)->withQueryString();

        return view('cashier-shifts.index', compact('shifts'));
    }

    public function show(CashierShift $cashierShift)
    {
        $cashierShift->load('sales.customer', 'user', 'cashAccount');

        return view('cashier-shifts.show', compact('cashierShift'));
    }

    public function openForm()
    {
        $existing = CashierShift::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($existing) {
            return redirect()->route('pos.index');
        }
        $cashAccounts = CashAccount::where('active', true)->where('type', 'cash')->get();

        return view('cashier-shifts.open', compact('cashAccounts'));
    }

    public function open(Request $request)
    {
        $data = $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        if (CashierShift::where('user_id', auth()->id())->where('status', 'open')->exists()) {
            return redirect()->route('pos.index')->with('error', 'Anda sudah memiliki shift yang aktif.');
        }

        CashierShift::create([
            'user_id' => auth()->id(),
            'cash_account_id' => $data['cash_account_id'],
            'opening_balance' => $data['opening_balance'],
            'expected_cash' => $data['opening_balance'],
            'status' => 'open',
            'opened_at' => now(),
        ]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(), 'action' => 'open_shift', 'module' => 'cashier_shift',
            'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift kasir berhasil dibuka.');
    }

    public function closeForm()
    {
        $shift = CashierShift::where('user_id', auth()->id())->where('status', 'open')->first();
        if (! $shift) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada shift kasir yang sedang aktif.');
        }
        $shift->expected_cash = (float) $shift->opening_balance + (float) $shift->cash_sales + (float) $shift->cash_in - (float) $shift->cash_out;

        return view('cashier-shifts.close', compact('shift'));
    }

    public function close(Request $request)
    {
        $shift = CashierShift::where('user_id', auth()->id())->where('status', 'open')->first();
        if (! $shift) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada shift kasir yang sedang aktif.');
        }

        $data = $request->validate(['actual_cash' => 'required|numeric|min:0', 'notes' => 'nullable|string']);
        $expected = (float) $shift->opening_balance + (float) $shift->cash_sales + (float) $shift->cash_in - (float) $shift->cash_out;

        DB::transaction(function () use ($shift, $data, $expected) {
            $shift->update([
                'expected_cash' => $expected,
                'actual_cash' => $data['actual_cash'],
                'difference' => $data['actual_cash'] - $expected,
                'status' => 'closed',
                'closed_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'close_shift', 'module' => 'cashier_shift',
                'record_type' => CashierShift::class, 'record_id' => $shift->id,
                'new_values' => ['expected' => $expected, 'actual' => $data['actual_cash'], 'difference' => $data['actual_cash'] - $expected],
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Shift kasir berhasil ditutup.');
    }
}
