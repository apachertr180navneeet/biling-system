<?php

namespace App\Http\Controllers\Admin\ChannelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtaSyncLog;
use Yajra\DataTables\Facades\DataTables;

class SyncLogController extends Controller
{
    public function index()
    {
        return view('admin.channel-manager.sync-logs.index');
    }

    public function data(Request $request)
    {
        $query = OtaSyncLog::with(['otaChannel', 'reservation'])->latest();

        return DataTables::of($query)
            ->filterColumn('ota_channel_name', function ($query, $value) {
                $query->whereHas('otaChannel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('reservation_number', function ($query, $value) {
                $query->whereHas('reservation', function ($q) use ($value) {
                    $q->where('reservation_number', 'like', "%{$value}%");
                });
            })
            ->addColumn('ota_channel_name', fn($log) => $log->otaChannel?->name ?? '-')
            ->addColumn('reservation_number', fn($log) => $log->reservation?->reservation_number ?? '-')
            ->addColumn('direction_badge', function ($log) {
                $class = $log->direction === 'inbound' ? 'bg-info' : 'bg-warning';
                return "<span class='badge {$class}'>{$log->direction}</span>";
            })
            ->addColumn('status_badge', function ($log) {
                $class = match($log->status) {
                    'success' => 'bg-success',
                    'failed' => 'bg-danger',
                    default => 'bg-secondary',
                };
                return "<span class='badge {$class}'>{$log->status}</span>";
            })
            ->rawColumns(['direction_badge', 'status_badge'])
            ->make(true);
    }
}
