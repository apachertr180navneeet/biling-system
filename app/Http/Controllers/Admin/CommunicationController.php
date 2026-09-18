<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommunicationTemplate;
use App\Models\CommunicationLog;
use App\Models\MessageTemplate;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CommunicationController extends Controller
{
    // Message Templates (the actual sendable templates)
    public function index()
    {
        return view('admin.communications.index');
    }

    public function data(Request $request)
    {
        $query = MessageTemplate::query();
        if ($request->channel) {
            $query->where('channel', $request->channel);
        }

        return DataTables::of($query)
            ->addColumn('channel_badge', function ($t) {
                $icons = ['email' => 'bx-envelope', 'sms' => 'bx-message-square', 'whatsapp' => 'bxl-whatsapp'];
                $colors = ['email' => 'primary', 'sms' => 'success', 'whatsapp' => 'success'];
                $icon = $icons[$t->channel] ?? 'bx-message';
                $color = $colors[$t->channel] ?? 'secondary';
                return '<span class="badge bg-' . $color . '"><i class="bx ' . $icon . '"></i> ' . strtoupper($t->channel) . '</span>';
            })
            ->addColumn('status_badge', function ($t) {
                return $t->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($t) {
                $editUrl = route('admin.communications.edit', $t);
                $deleteUrl = route('admin.communications.destroy', $t);
                $previewUrl = route('admin.communications.preview', $t);
                return "
                    <div class='btn-group btn-group-sm'>
                        <a href='{$previewUrl}' class='btn btn-info' title='Preview'><i class='bx bx-show'></i></a>
                        <a href='{$editUrl}' class='btn btn-primary' title='Edit'><i class='bx bx-edit'></i></a>
                        <button class='btn btn-danger btn-delete-item' data-url='{$deleteUrl}' title='Delete'><i class='bx bx-trash'></i></button>
                    </div>";
            })
            ->rawColumns(['channel_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $template = null;
        return view('admin.communications.form', compact('template'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'channel' => 'required|in:email,sms,whatsapp',
                'event' => 'required|string|max:255|unique:message_templates,event',
                'subject' => 'required_if:channel,email|nullable|string',
                'body' => 'required|string',
                'variables' => 'nullable|array',
            ]);

            MessageTemplate::create($request->all());
            return redirect()->route('admin.communications.index')->with('success', 'Template created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(MessageTemplate $template)
    {
        return view('admin.communications.form', compact('template'));
    }

    public function update(Request $request, MessageTemplate $template)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'channel' => 'required|in:email,sms,whatsapp',
                'event' => 'required|string|max:255|unique:message_templates,event,' . $template->id,
                'subject' => 'required_if:channel,email|nullable|string',
                'body' => 'required|string',
                'variables' => 'nullable|array',
            ]);

            $template->update($request->all());
            return redirect()->route('admin.communications.index')->with('success', 'Template updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, MessageTemplate $template)
    {
        $template->delete();
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Template deleted!']);
        }
        return redirect()->route('admin.communications.index')->with('success', 'Template deleted!');
    }

    public function preview(MessageTemplate $template)
    {
        $sampleData = [
            'guest_name' => 'John Smith',
            'reservation_number' => 'RES-00001',
            'hotel_name' => 'Grand Hotel',
            'check_in_date' => '17 Jul 2026',
            'check_out_date' => '20 Jul 2026',
            'room_number' => '101',
            'amount' => '5,000.00',
            'otp' => '123456',
            'booking_url' => '#',
        ];
        $renderedBody = $template->render($sampleData);
        return view('admin.communications.preview', compact('template', 'renderedBody', 'sampleData'));
    }

    // Communication Logs
    public function logs()
    {
        return view('admin.communications.logs');
    }

    public function logsData(Request $request)
    {
        $query = CommunicationLog::query();
        if ($request->channel) {
            $query->where('channel', $request->channel);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('channel_badge', function ($log) {
                $colors = ['email' => 'primary', 'sms' => 'success', 'whatsapp' => 'success'];
                $color = $colors[$log->channel] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . strtoupper($log->channel) . '</span>';
            })
            ->addColumn('status_badge', function ($log) {
                $colors = ['pending' => 'warning', 'sent' => 'info', 'failed' => 'danger', 'delivered' => 'success', 'read' => 'success'];
                $color = $colors[$log->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($log->status) . '</span>';
            })
            ->addColumn('formatted_date', fn($log) => $log->created_at?->format('d M Y, h:i A') ?? '')
            ->rawColumns(['channel_badge', 'status_badge'])
            ->make(true);
    }
}
