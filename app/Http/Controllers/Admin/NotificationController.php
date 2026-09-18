<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    public function index()
    {
        return view('admin.notifications.index');
    }

    public function data(Request $request)
    {
        $query = UserNotification::where('user_id', auth()->id())->latest();

        return DataTables::of($query)
            ->addColumn('type_badge', function ($n) {
                $colors = ['info' => 'info', 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger'];
                $color = $colors[$n->type] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($n->type) . '</span>';
            })
            ->addColumn('status_badge', function ($n) {
                return $n->is_read
                    ? '<span class="badge bg-secondary">Read</span>'
                    : '<span class="badge bg-primary">Unread</span>';
            })
            ->addColumn('formatted_date', fn($n) => $n->created_at?->format('d M Y, h:i A') ?? '')
            ->rawColumns(['type_badge', 'status_badge'])
            ->make(true);
    }

    public function markAsRead(Request $request, UserNotification $notification)
    {
        $notification->update(['is_read' => true]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back();
    }

    public function markAllAsRead(Request $request)
    {
        UserNotification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'All notifications marked as read!');
    }

    public function getUnreadCount(Request $request)
    {
        $count = UserNotification::where('user_id', auth()->id())->unread()->count();
        return response()->json(['count' => $count]);
    }

    public function getRecent(Request $request)
    {
        $notifications = UserNotification::where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }
}
