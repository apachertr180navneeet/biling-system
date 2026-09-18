<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingCampaign;
use App\Models\MarketingCampaignLog;
use App\Models\Guest;
use App\Models\Hotel;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CampaignController extends Controller
{
    public function index()
    {
        return view('admin.crm.campaigns.index');
    }

    public function data(Request $request)
    {
        $query = MarketingCampaign::select('marketing_campaigns.*');
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('edit_url', fn($c) => route('admin.crm.campaigns.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.crm.campaigns.destroy', $c))
            ->addColumn('show_url', fn($c) => route('admin.crm.campaigns.show', $c))
            ->addColumn('send_url', fn($c) => route('admin.crm.campaigns.send', $c))
            ->rawColumns([])
            ->make(true);
    }

    public function show(MarketingCampaign $campaign)
    {
        $campaign->load('logs.guest');
        return view('admin.crm.campaigns.show', compact('campaign'));
    }

    public function create()
    {
        $campaign = null;
        return view('admin.crm.campaigns.form', compact('campaign'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'channel' => 'required|in:email,sms,whatsapp',
                'subject' => 'nullable|required_if:channel,email|string|max:255',
                'content' => 'required|string',
                'target_audience' => 'required|in:all_guests,loyalty_members,recent_guests',
                'scheduled_at' => 'nullable|date',
                'status' => 'required|in:draft,scheduled',
            ]);

            MarketingCampaign::create([
                'hotel_id' => auth()->user()->branch_id ?? Hotel::first()?->id,
                'name' => $request->name,
                'channel' => $request->channel,
                'subject' => $request->subject,
                'content' => $request->content,
                'target_audience' => $request->target_audience,
                'scheduled_at' => $request->scheduled_at,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.crm.campaigns.index')->with('success', 'Campaign created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(MarketingCampaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return redirect()->route('admin.crm.campaigns.index')->with('error', 'Sent campaigns cannot be edited.');
        }
        return view('admin.crm.campaigns.form', compact('campaign'));
    }

    public function update(Request $request, MarketingCampaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return redirect()->route('admin.crm.campaigns.index')->with('error', 'Sent campaigns cannot be updated.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'channel' => 'required|in:email,sms,whatsapp',
                'subject' => 'nullable|required_if:channel,email|string|max:255',
                'content' => 'required|string',
                'target_audience' => 'required|in:all_guests,loyalty_members,recent_guests',
                'scheduled_at' => 'nullable|date',
                'status' => 'required|in:draft,scheduled',
            ]);

            $campaign->update([
                'name' => $request->name,
                'channel' => $request->channel,
                'subject' => $request->subject,
                'content' => $request->content,
                'target_audience' => $request->target_audience,
                'scheduled_at' => $request->scheduled_at,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.crm.campaigns.index')->with('success', 'Campaign updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(MarketingCampaign $campaign)
    {
        try {
            $campaign->delete();
            return response()->json(['success' => true, 'message' => 'Campaign deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function send(MarketingCampaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return redirect()->back()->with('error', 'This campaign has already been sent.');
        }

        try {
            // Gather target guests
            $guestsQuery = Guest::query();

            if ($campaign->target_audience === 'loyalty_members') {
                $guestsQuery->whereHas('loyaltyMember');
            } elseif ($campaign->target_audience === 'recent_guests') {
                $guestsQuery->latest()->take(15);
            }

            $guests = $guestsQuery->get();

            if ($guests->isEmpty()) {
                return redirect()->back()->with('error', 'No recipients found matching the target audience.');
            }

            $campaign->update(['status' => 'sending']);

            $total = 0;
            $success = 0;

            foreach ($guests as $guest) {
                $contact = $campaign->channel === 'email' ? $guest->email : $guest->phone;
                $status = 'sent';
                $error = null;

                if (empty($contact)) {
                    $status = 'failed';
                    $error = 'Contact information is missing.';
                    $contact = 'N/A';
                }

                MarketingCampaignLog::create([
                    'campaign_id' => $campaign->id,
                    'guest_id' => $guest->id,
                    'recipient_contact' => $contact,
                    'status' => $status,
                    'error_message' => $error,
                    'sent_at' => now(),
                ]);

                $total++;
                if ($status === 'sent') {
                    $success++;
                }
            }

            $campaign->update([
                'status' => 'sent',
                'sent_at' => now(),
                'total_recipients' => $total,
                'successful_deliveries' => $success,
            ]);

            return redirect()->route('admin.crm.campaigns.show', $campaign)->with('success', "Campaign sent successfully to {$success} / {$total} recipients.");
        } catch (Exception $e) {
            $campaign->update(['status' => 'failed']);
            return redirect()->back()->with('error', 'Campaign execution failed: ' . $e->getMessage());
        }
    }
}
