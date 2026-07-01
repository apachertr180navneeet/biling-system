<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceReminder;
use App\Models\Customer;
use Illuminate\Http\Request;

class ServiceReminderController extends Controller
{
    public function index()
    {
        $reminders = ServiceReminder::with('customer')->orderBy('next_service_date')->paginate(20);
        return view('admin.service_reminders.index', compact('reminders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.service_reminders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_number' => 'nullable|string|max:50',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'required|date',
            'reminder_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        ServiceReminder::create($data);
        return redirect()->route('admin.service-reminders.index')->withSuccess('Reminder created successfully.');
    }

    public function edit(ServiceReminder $serviceReminder)
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.service_reminders.edit', compact('serviceReminder', 'customers'));
    }

    public function update(Request $request, ServiceReminder $serviceReminder)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_number' => 'nullable|string|max:50',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'required|date',
            'reminder_date' => 'nullable|date',
            'status' => 'required|in:pending,sent,completed',
            'notes' => 'nullable|string',
        ]);
        $serviceReminder->update($data);
        return redirect()->route('admin.service-reminders.index')->withSuccess('Reminder updated successfully.');
    }

    public function destroy(ServiceReminder $serviceReminder)
    {
        $serviceReminder->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(ServiceReminder $serviceReminder)
    {
        $serviceReminder->update(['is_active' => !$serviceReminder->is_active]);
        return response()->json(['success' => true, 'is_active' => $serviceReminder->is_active]);
    }

    public function updateStatus(ServiceReminder $serviceReminder, Request $request)
    {
        $request->validate(['status' => 'required|in:pending,sent,completed']);
        $serviceReminder->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $serviceReminder->status]);
    }
}
