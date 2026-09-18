<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\RatePlan;
use App\Models\Testimonial;
use App\Models\SiteFeature;

class HomeController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')
            ->with(['rooms.roomType'])
            ->withCount('rooms')
            ->get();

        $totalRooms = $hotels->sum('rooms_count');
        $distinctCities = Hotel::where('status', 'active')
            ->whereNotNull('city')
            ->distinct('city')
            ->count('city');

        $testimonials = Testimonial::where('status', 'active')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $features = SiteFeature::forSection('home_features')->get();

        return view('web.home.index', [
            'hotels' => $hotels,
            'totalRooms' => $totalRooms,
            'distinctCities' => $distinctCities,
            'testimonials' => $testimonials,
            'homeFeatures' => $features,
        ]);
    }

    public function hotels()
    {
        $hotels = Hotel::where('status', 'active')
            ->with(['rooms.roomType'])
            ->withCount('rooms')
            ->get();

        return view('web.hotels.index', [
            'hotels' => $hotels,
        ]);
    }

    public function hotelShow(Hotel $hotel)
    {
        $hotel->load(['rooms.roomType', 'rooms.bedType', 'rooms.roomStatus']);

        $roomTypes = RoomType::where('status', 'active')->get();

        return view('web.hotels.show', [
            'hotel' => $hotel,
            'roomTypes' => $roomTypes,
        ]);
    }

    public function about()
    {
        $milestones = \App\Models\Milestone::orderBy('sort_order')->get();
        $values = SiteFeature::forSection('about_values')->get();

        return view('web.about.index', [
            'milestones' => $milestones,
            'values' => $values,
        ]);
    }

    public function contact()
    {
        return view('web.contact.index');
    }
}
