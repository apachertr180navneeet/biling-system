<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Yajra\DataTables\Facades\DataTables;

class AuditLogController extends Controller
{
    public function index()
    {
        return view('admin.audit-logs.index');
    }

    public function data(Request $request)
    {
        $query = ActivityLog::query()->with('causer');

        if ($request->module) {
            $query->where('properties->module', $request->module);
        }
        if ($request->event) {
            $query->where('event', $request->event);
        }

        return DataTables::of($query)
            ->addColumn('causer_name', fn($log) => $log->causer?->name ?? 'System')
            ->addColumn('module_badge', function ($log) {
                $module = $log->properties['module'] ?? 'system';
                return '<span class="badge bg-secondary">' . ucfirst($module) . '</span>';
            })
            ->addColumn('event_badge', function ($log) {
                $colors = ['created' => 'success', 'updated' => 'warning', 'deleted' => 'danger'];
                $color = $colors[$log->event] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($log->event) . '</span>';
            })
            ->addColumn('formatted_date', fn($log) => $log->created_at?->format('d M Y, h:i A') ?? '')
            ->rawColumns(['module_badge', 'event_badge'])
            ->make(true);
    }

    public function show(ActivityLog $auditLog)
    {
        $auditLog->load('causer');
        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
