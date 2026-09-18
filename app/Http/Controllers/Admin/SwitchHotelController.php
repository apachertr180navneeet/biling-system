<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;

class SwitchHotelController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
        ]);

        session(['active_hotel_id' => $request->hotel_id]);
        return redirect()->back()->with('success', 'Property switched successfully!');
    }

    public function getActiveHotel()
    {
        $hotelId = session('active_hotel_id');
        return Hotel::find($hotelId);
    }
}
