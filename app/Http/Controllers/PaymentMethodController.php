<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('name')->paginate(15);

        return view('payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment-methods.form', ['paymentMethod' => new PaymentMethod]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        PaymentMethod::create($data);

        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.form', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $this->validated($request, $paymentMethod->id);
        $paymentMethod->update($data);

        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->salePayments()->exists()) {
            return back()->with('error', 'Metode pembayaran tidak dapat dihapus karena masih digunakan.');
        }
        $paymentMethod->delete();

        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:payment_methods,code'.($id ? ",{$id}" : ''),
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank_transfer,qris,ewallet,credit,other',
            'account_number' => 'nullable|string|max:50',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
