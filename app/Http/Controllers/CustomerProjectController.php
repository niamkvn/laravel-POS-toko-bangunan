<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerProject;
use Illuminate\Http\Request;

class CustomerProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = CustomerProject::with('customer')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        return view('customer-projects.index', compact('projects'));
    }

    public function create()
    {
        return view('customer-projects.form', [
            'project' => new CustomerProject,
            'customers' => Customer::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['project_code'] = 'PRJ-'.str_pad((CustomerProject::max('id') + 1), 5, '0', STR_PAD_LEFT);
        CustomerProject::create($data);

        return redirect()->route('customer-projects.index')->with('success', 'Project berhasil ditambahkan.');
    }

    public function show(CustomerProject $customerProject)
    {
        $customerProject->load('customer', 'sales', 'quotations', 'salesOrders');

        $totalSales = $customerProject->sales()->where('status', '!=', 'void')->sum('total');
        $totalPaid = $customerProject->sales()->where('status', '!=', 'void')->sum('paid_amount');
        $outstanding = $totalSales - $totalPaid;

        return view('customer-projects.show', compact('customerProject', 'totalSales', 'totalPaid', 'outstanding'));
    }

    public function edit(CustomerProject $customerProject)
    {
        return view('customer-projects.form', [
            'project' => $customerProject,
            'customers' => Customer::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, CustomerProject $customerProject)
    {
        $customerProject->update($this->validated($request));

        return redirect()->route('customer-projects.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(CustomerProject $customerProject)
    {
        if ($customerProject->sales()->exists()) {
            return back()->with('error', 'Project tidak dapat dihapus karena memiliki transaksi.');
        }
        $customerProject->delete();

        return back()->with('success', 'Project berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:200',
            'location' => 'nullable|string|max:200',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:planning,ongoing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
    }
}
