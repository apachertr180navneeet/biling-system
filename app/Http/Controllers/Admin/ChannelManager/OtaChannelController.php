<?php

namespace App\Http\Controllers\Admin\ChannelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtaChannel;
use App\Models\Hotel;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class OtaChannelController extends Controller
{
    public function index()
    {
        return view('admin.channel-manager.channels.index');
    }

    public function data(Request $request)
    {
        $query = OtaChannel::with('hotel')->latest();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('hotel_name', fn($channel) => $channel->hotel?->name ?? '-')
            ->addColumn('provider_label', fn($channel) => match($channel->provider) {
                'booking_com' => 'Booking.com',
                'expedia' => 'Expedia',
                'agoda' => 'Agoda',
                'airbnb' => 'Airbnb',
                'makemytrip' => 'MakeMyTrip',
                'goibibo' => 'Goibibo',
                'trip_com' => 'Trip.com',
                'hostelworld' => 'Hostelworld',
                default => ucfirst(str_replace('_', ' ', $channel->provider)),
            })
            ->addColumn('last_sync', fn($channel) => $channel->last_synced_at?->diffForHumans() ?? 'Never')
            ->addColumn('status_url', fn($channel) => route('admin.channel-manager.channels.status', $channel))
            ->addColumn('edit_url', fn($channel) => route('admin.channel-manager.channels.edit', $channel))
            ->addColumn('delete_url', fn($channel) => route('admin.channel-manager.channels.destroy', $channel))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $channel = null;
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.channel-manager.channels.form', compact('channel', 'hotels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'provider' => 'required|in:booking_com,expedia,agoda,airbnb,makemytrip,goibibo,trip_com,hostelworld',
                'api_key' => 'nullable|string',
                'api_secret' => 'nullable|string',
                'property_id_on_ota' => 'nullable|string',
                'endpoint_url' => 'nullable|url',
            ]);

            OtaChannel::create($request->only([
                'hotel_id', 'name', 'provider', 'api_key', 'api_secret',
                'property_id_on_ota', 'endpoint_url', 'sync_rates', 'sync_availability',
                'sync_reservations', 'auto_sync',
            ]));

            return redirect()->route('admin.channel-manager.channels.index')->with('success', 'OTA Channel created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(OtaChannel $channel)
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.channel-manager.channels.form', compact('channel', 'hotels'));
    }

    public function update(Request $request, OtaChannel $channel)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'provider' => 'required|in:booking_com,expedia,agoda,airbnb,makemytrip,goibibo,trip_com,hostelworld',
                'api_key' => 'nullable|string',
                'api_secret' => 'nullable|string',
                'property_id_on_ota' => 'nullable|string',
                'endpoint_url' => 'nullable|url',
            ]);

            $channel->update($request->only([
                'hotel_id', 'name', 'provider', 'api_key', 'api_secret',
                'property_id_on_ota', 'endpoint_url', 'sync_rates', 'sync_availability',
                'sync_reservations', 'auto_sync', 'status',
            ]));

            return redirect()->route('admin.channel-manager.channels.index')->with('success', 'OTA Channel updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, OtaChannel $channel)
    {
        try {
            $channel->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'OTA Channel deleted successfully!']);
            }
            return redirect()->route('admin.channel-manager.channels.index')->with('success', 'OTA Channel deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, OtaChannel $channel)
    {
        try {
            $channel->status = $channel->status === 'active' ? 'inactive' : 'active';
            $channel->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $channel->status, 'message' => 'Channel status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Channel status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function settings()
    {
        $channels = OtaChannel::with('hotel')->get();

        $allProviders = [
            'booking_com' => ['name' => 'Booking.com', 'icon' => 'bx-globe', 'color' => '#003580'],
            'expedia' => ['name' => 'Expedia', 'icon' => 'bx-plane-alt', 'color' => '#FBCE04'],
            'agoda' => ['name' => 'Agoda', 'icon' => 'bx-hotel', 'color' => '#5C2D91'],
            'airbnb' => ['name' => 'Airbnb', 'icon' => 'bx-home-heart', 'color' => '#FF5A5F'],
            'makemytrip' => ['name' => 'MakeMyTrip', 'icon' => 'bx-trip', 'color' => '#E23738'],
            'goibibo' => ['name' => 'Goibibo', 'icon' => 'bx-map', 'color' => '#F05A28'],
            'trip_com' => ['name' => 'Trip.com', 'icon' => 'bx-travel', 'color' => '#287DFA'],
            'hostelworld' => ['name' => 'Hostelworld', 'icon' => 'bx-building-house', 'color' => '#2B9EB3'],
        ];

        return view('admin.channel-manager.settings', compact('channels', 'allProviders'));
    }

    public function updateSettings(Request $request)
    {
        try {
            $hotelId = $request->input('hotel_id');
            $providers = $request->input('providers', []);

            foreach ($providers as $provider => $data) {
                $existing = OtaChannel::where('hotel_id', $hotelId)
                    ->where('provider', $provider)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'status' => $data['enabled'] ? 'active' : 'inactive',
                        'api_key' => $data['api_key'] ?? $existing->api_key,
                        'api_secret' => $data['api_secret'] ?? $existing->api_secret,
                        'property_id_on_ota' => $data['property_id'] ?? $existing->property_id_on_ota,
                        'endpoint_url' => $data['endpoint_url'] ?? $existing->endpoint_url,
                    ]);
                } elseif ($data['enabled'] ?? false) {
                    $providerNames = [
                        'booking_com' => 'Booking.com',
                        'expedia' => 'Expedia',
                        'agoda' => 'Agoda',
                        'airbnb' => 'Airbnb',
                        'makemytrip' => 'MakeMyTrip',
                        'goibibo' => 'Goibibo',
                        'trip_com' => 'Trip.com',
                        'hostelworld' => 'Hostelworld',
                    ];

                    OtaChannel::create([
                        'hotel_id' => $hotelId,
                        'name' => $providerNames[$provider] ?? $provider,
                        'provider' => $provider,
                        'api_key' => $data['api_key'] ?? null,
                        'api_secret' => $data['api_secret'] ?? null,
                        'property_id_on_ota' => $data['property_id'] ?? null,
                        'endpoint_url' => $data['endpoint_url'] ?? null,
                        'status' => 'active',
                    ]);
                }
            }

            return redirect()->route('admin.channel-manager.settings')->with('success', 'Channel settings updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
