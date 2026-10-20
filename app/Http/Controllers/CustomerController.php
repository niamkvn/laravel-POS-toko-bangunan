<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPrice;
use App\Models\PriceLevel;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('customer_code', 'like', "%{$request->search}%"))
            ->when($request->customer_type, fn ($q) => $q->where('customer_type', $request->customer_type))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.form', [
            'customer' => new Customer,
            'priceLevels' => PriceLevel::pluck('name', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['customer_code'] = 'CUST-'.str_pad((Customer::max('id') + 1), 5, '0', STR_PAD_LEFT);
        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['priceLevel', 'projects', 'specialPrices.product']);
        $sales = $customer->sales()->latest('sale_date')->paginate(10);
        $products = Product::orderBy('name')->pluck('name', 'id');

        return view('customers.show', compact('customer', 'sales', 'products'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', [
            'customer' => $customer,
            'priceLevels' => PriceLevel::pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request, $customer->id));

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->exists()) {
            return back()->with('error', 'Customer tidak dapat dihapus karena memiliki riwayat transaksi.');
        }
        $customer->delete();

        return back()->with('success', 'Customer berhasil dihapus.');
    }

    public function receivables(Request $request)
    {
        $sales = Sale::with('customer')
            ->where('is_credit', true)->where('status', '!=', 'void')
            ->when($request->customer_id, fn ($q) => $q->where('customer_id', $request->customer_id))
            ->orderBy('due_date')
            ->get()
            ->filter(fn ($s) => $s->outstanding() > 0);

        $customers = Customer::orderBy('name')->pluck('name', 'id');

        $aging = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, 'over_90' => 0];
        foreach ($sales as $sale) {
            $days = $sale->due_date ? now()->diffInDays($sale->due_date, false) * -1 : 0;
            $amount = $sale->outstanding();
            if ($days <= 0) {
                $aging['current'] += $amount;
            } elseif ($days <= 30) {
                $aging['1_30'] += $amount;
            } elseif ($days <= 60) {
                $aging['31_60'] += $amount;
            } elseif ($days <= 90) {
                $aging['61_90'] += $amount;
            } else {
                $aging['over_90'] += $amount;
            }
        }

        return view('customers.receivables', compact('sales', 'customers', 'aging'));
    }

    public function receivePayment(Request $request, Sale $sale)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$sale->outstanding(),
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $sale) {
            $sale->payments()->create([
                'payment_method_id' => $request->payment_method_id,
                'amount' => $request->amount,
                'reference_number' => $request->reference_number,
                'paid_at' => now(),
                'user_id' => auth()->id(),
            ]);

            $sale->increment('paid_amount', $request->amount);

            $cashAccount = \App\Models\CashAccount::first();
            if ($cashAccount) {
                $cashAccount->transactions()->create([
                    'type' => 'in',
                    'amount' => $request->amount,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'category' => 'payment_receivable',
                    'description' => "Pembayaran piutang {$sale->invoice_number}",
                    'transaction_date' => now(),
                    'user_id' => auth()->id(),
                ]);
                $cashAccount->increment('current_balance', $request->amount);
            }

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'payment', 'module' => 'receivable',
                'record_type' => Sale::class, 'record_id' => $sale->id,
                'new_values' => ['amount' => $request->amount],
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);
        });

        return back()->with('success', 'Pembayaran piutang berhasil dicatat.');
    }

    public function storeSpecialPrice(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_qty' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ]);
        $data['customer_id'] = $customer->id;
        $data['active'] = true;
        CustomerPrice::create($data);

        return back()->with('success', 'Harga khusus berhasil ditambahkan.');
    }

    public function destroySpecialPrice(CustomerPrice $customerPrice)
    {
        $customerId = $customerPrice->customer_id;
        $customerPrice->delete();

        return redirect()->route('customers.show', $customerId)->with('success', 'Harga khusus berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'customer_type' => 'required|in:umum,tukang,reseller,kontraktor,developer,perusahaan',
            'price_level_id' => 'nullable|exists:price_levels,id',
            'credit_limit' => 'required|numeric|min:0',
            'payment_term_days' => 'required|integer|min:0',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
