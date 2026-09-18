<?php

namespace App\Http\Controllers\Admin\ChannelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtaChannel;
use App\Services\ChannelManagerService;
use Exception;

class SyncController extends Controller
{
    public function __construct(private ChannelManagerService $channelManager)
    {
    }

    public function syncAll(Request $request)
    {
        try {
            $channels = OtaChannel::where('status', 'active')->get();

            if ($channels->isEmpty()) {
                return back()->with('error', 'No active OTA channels found.');
            }

            $results = [];
            foreach ($channels as $channel) {
                $results[$channel->name] = $this->channelManager->syncChannel($channel);
            }

            return back()->with('success', 'Sync completed for ' . $channels->count() . ' channel(s).');
        } catch (Exception $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    public function syncChannel(Request $request, OtaChannel $channel)
    {
        try {
            $result = $this->channelManager->syncChannel($channel);

            return back()->with('success', "Sync completed for {$channel->name}. Reservations pulled: {$result['reservations']}, Rates pushed: {$result['rates']}");
        } catch (Exception $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}
