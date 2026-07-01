<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('customer', 'invoice')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $invoices = Invoice::where('status', 'confirmed')->orderBy('created_at', 'desc')->get();
        return view('admin.payments.create', compact('customers', 'invoices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque,upi',
            'reference_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $last = Payment::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $data['payment_number'] = 'PAY-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Payment::create($data);
        return redirect()->route('admin.payments.index')->withSuccess('Payment recorded successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(['success' => true, 'message' => 'Payment deleted successfully.']);
    }
}
