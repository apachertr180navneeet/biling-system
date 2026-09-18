<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceWorkOrder;
use App\Models\MaintenanceAsset;
use App\Models\Employee;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class WorkOrderController extends Controller
{
    public function index()
    {
        return view('admin.maintenance.work-orders.index');
    }

    public function data(Request $request)
    {
        $query = MaintenanceWorkOrder::with(['asset', 'assignedEmployee'])->select('maintenance_work_orders.*');
        return DataTables::of($query)
            ->filterColumn('work_order_number', function ($query, $value) {
                $query->where('work_order_number', 'like', "%{$value}%");
            })
            ->filterColumn('title', function ($query, $value) {
                $query->where('title', 'like', "%{$value}%");
            })
            ->addColumn('asset_name', fn($wo) => $wo->asset?->name ?? '-')
            ->addColumn('employee_name', fn($wo) => $wo->assignedEmployee ? $wo->assignedEmployee->first_name . ' ' . $wo->assignedEmployee->last_name : '-')
            ->addColumn('edit_url', fn($wo) => route('admin.maintenance.work-orders.edit', $wo))
            ->addColumn('delete_url', fn($wo) => route('admin.maintenance.work-orders.destroy', $wo))
            ->addColumn('show_url', fn($wo) => route('admin.maintenance.work-orders.show', $wo))
            ->addColumn('status_url', fn($wo) => route('admin.maintenance.work-orders.status', $wo))
            ->rawColumns([])
            ->make(true);
    }

    public function show(MaintenanceWorkOrder $work_order)
    {
        $work_order->load('asset', 'assignedEmployee');
        return view('admin.maintenance.work-orders.show', ['workOrder' => $work_order]);
    }

    public function create()
    {
        $workOrder = null;
        $assets = MaintenanceAsset::orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('admin.maintenance.work-orders.form', compact('workOrder', 'assets', 'employees'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'asset_id' => 'nullable|exists:maintenance_assets,id',
                'title' => 'required|string|max:255',
                'type' => 'required|in:preventive,breakdown',
                'priority' => 'required|in:low,medium,high',
                'assigned_employee_id' => 'nullable|exists:employees,id',
                'schedule_date' => 'nullable|date',
                'completion_date' => 'nullable|date',
                'cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'status' => 'required|in:pending,in_progress,completed,cancelled',
            ]);

            MaintenanceWorkOrder::create([
                'hotel_id' => auth()->user()->branch_id ?? Hotel::first()?->id,
                'work_order_number' => Helper::slug('maintenance_work_orders', 'WO-' . now()->format('YmdHis'), 'work_order_number'),
                'asset_id' => $request->asset_id,
                'title' => $request->title,
                'type' => $request->type,
                'priority' => $request->priority,
                'assigned_employee_id' => $request->assigned_employee_id,
                'schedule_date' => $request->schedule_date,
                'completion_date' => $request->completion_date,
                'cost' => $request->cost ?? 0,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.maintenance.work-orders.index')->with('success', 'Work Order created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(MaintenanceWorkOrder $work_order)
    {
        $workOrder = $work_order;
        $assets = MaintenanceAsset::orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('admin.maintenance.work-orders.form', compact('workOrder', 'assets', 'employees'));
    }

    public function update(Request $request, MaintenanceWorkOrder $work_order)
    {
        try {
            $request->validate([
                'asset_id' => 'nullable|exists:maintenance_assets,id',
                'title' => 'required|string|max:255',
                'type' => 'required|in:preventive,breakdown',
                'priority' => 'required|in:low,medium,high',
                'assigned_employee_id' => 'nullable|exists:employees,id',
                'schedule_date' => 'nullable|date',
                'completion_date' => 'nullable|date',
                'cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'status' => 'required|in:pending,in_progress,completed,cancelled',
            ]);

            $work_order->update([
                'asset_id' => $request->asset_id,
                'title' => $request->title,
                'type' => $request->type,
                'priority' => $request->priority,
                'assigned_employee_id' => $request->assigned_employee_id,
                'schedule_date' => $request->schedule_date,
                'completion_date' => $request->completion_date,
                'cost' => $request->cost ?? 0,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.maintenance.work-orders.index')->with('success', 'Work Order updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(MaintenanceWorkOrder $work_order)
    {
        try {
            $work_order->delete();
            return response()->json(['success' => true, 'message' => 'Work Order deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function status(MaintenanceWorkOrder $work_order)
    {
        $statusFlow = [
            'pending' => 'in_progress',
            'in_progress' => 'completed',
        ];
        $next = $statusFlow[$work_order->status] ?? null;
        if ($next) {
            $data = ['status' => $next];
            if ($next === 'completed') {
                $data['completion_date'] = now()->toDateString();
            }
            $work_order->update($data);
            return response()->json(['success' => true, 'message' => 'Status updated to ' . str_replace('_', ' ', $next)]);
        }
        return response()->json(['success' => false, 'message' => 'Cannot change status.'], 422);
    }
}
