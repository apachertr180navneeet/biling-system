<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('first_name')->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:individual,corporate',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|digits:10',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15',
            'pan_no' => 'nullable|string|max:10',
            'aadhaar_no' => 'nullable|string|max:12',
        ]);
        Customer::create($data);
        return redirect()->route('admin.customers.index')->withSuccess('Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'type' => 'required|in:individual,corporate',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|digits:10',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'state' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15',
            'pan_no' => 'nullable|string|max:10',
            'aadhaar_no' => 'nullable|string|max:12',
        ]);
        $customer->update($data);
        return redirect()->route('admin.customers.index')->withSuccess('Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        return response()->json(['success' => true, 'is_active' => $customer->fresh()->is_active]);
    }
}
