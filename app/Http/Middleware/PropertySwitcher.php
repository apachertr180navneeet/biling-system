<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Hotel;

class PropertySwitcher
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return $next($request);
        }

        if ($request->isMethod('post') && $request->has('switch_hotel')) {
            $hotelId = $request->input('active_hotel_id');
            if ($hotelId && Hotel::where('id', $hotelId)->exists()) {
                session(['active_hotel_id' => $hotelId]);
            } else {
                session()->forget('active_hotel_id');
            }
            return redirect()->back();
        }

        if (!session('active_hotel_id')) {
            $firstHotel = Hotel::where('status', 'active')->first();
            if ($firstHotel) {
                session(['active_hotel_id' => $firstHotel->id]);
            }
        }

        return $next($request);
    }
}
