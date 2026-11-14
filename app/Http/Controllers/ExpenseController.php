<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\NumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    public function __construct(private NumberingService $numbering) {}

    public function index(Request $request)
    {
        $expenses = Expense::with('category', 'cashAccount')
            ->when($request->date_from, fn ($q) => $q->whereDate('expense_date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('expense_date', '<=', $request->date_to))
            ->latest('expense_date')->paginate(15)->withQueryString();

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create', [
            'categories' => ExpenseCategory::where('active', true)->pluck('name', 'id'),
            'cashAccounts' => CashAccount::where('active', true)->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $account = CashAccount::findOrFail($data['cash_account_id']);
                if ($account->current_balance < $data['amount']) {
                    throw ValidationException::withMessages(['amount' => 'Saldo kas tidak mencukupi untuk pengeluaran ini.']);
                }

                $expense = Expense::create([
                    ...$data,
                    'expense_number' => $this->numbering->next('EXP'),
                    'user_id' => auth()->id(),
                ]);

                $account->decrement('current_balance', $data['amount']);
                $account->transactions()->create([
                    'type' => 'out',
                    'amount' => $data['amount'],
                    'reference_type' => Expense::class,
                    'reference_id' => $expense->id,
                    'category' => 'expense',
                    'description' => $data['description'],
                    'transaction_date' => $data['expense_date'],
                    'user_id' => auth()->id(),
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function show(Expense $expense)
    {
        $expense->load('category', 'cashAccount', 'user');

        return view('expenses.show', compact('expense'));
    }
}
