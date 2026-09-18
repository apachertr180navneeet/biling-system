<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\NightAudit;
use Carbon\Carbon;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_companies' => Company::count(),
        ];

        $hotelKpis = $this->getHotelKpis();

        return view('admin.dashboard.index', compact('user', 'stats', 'hotelKpis'));
    }

    private function getHotelKpis()
    {
        $hotels = Hotel::where('status', 'active')->get();
        if ($hotels->isEmpty()) {
            return $this->emptyKpis();
        }

        $today = now()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $prevMonthStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $prevMonthEnd = now()->subMonth()->endOfMonth()->format('Y-m-d');

        $totalRooms = Room::where('status', 'active')->count();
        if ($totalRooms == 0) {
            return $this->emptyKpis();
        }

        $todayAudit = NightAudit::where('audit_date', $today)->first();
        $todayOccupied = $todayAudit->total_rooms_occupied ?? 0;
        $todayOccupancy = round(($todayOccupied / $totalRooms) * 100, 1);

        $todayRevenue = ReservationRoom::whereHas('reservation', function ($q) use ($today) {
            $q->where('check_in_date', $today)->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        })->sum('total_amount');

        $todayRoomsSold = ReservationRoom::whereHas('reservation', function ($q) use ($today) {
            $q->where('check_in_date', $today)->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        })->count();

        $todayAdr = $todayRoomsSold > 0 ? round($todayRevenue / $todayRoomsSold, 2) : 0;
        $todayRevpar = round($todayRevenue / $totalRooms, 2);

        $monthRevenue = ReservationRoom::whereHas('reservation', function ($q) use ($monthStart, $today) {
            $q->whereBetween('check_in_date', [$monthStart, $today])->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        })->sum('total_amount');

        $monthRoomsSold = ReservationRoom::whereHas('reservation', function ($q) use ($monthStart, $today) {
            $q->whereBetween('check_in_date', [$monthStart, $today])->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        })->count();

        $monthDays = now()->day;
        $monthTotalRoomNights = $totalRooms * $monthDays;

        $monthOccupied = NightAudit::whereBetween('audit_date', [$monthStart, $today])->sum('total_rooms_occupied');
        $monthOccupancy = $monthTotalRoomNights > 0 ? round(($monthOccupied / $monthTotalRoomNights) * 100, 1) : 0;
        $monthAdr = $monthRoomsSold > 0 ? round($monthRevenue / $monthRoomsSold, 2) : 0;
        $monthRevpar = $monthTotalRoomNights > 0 ? round($monthRevenue / $monthTotalRoomNights, 2) : 0;

        $prevMonthRevenue = ReservationRoom::whereHas('reservation', function ($q) use ($prevMonthStart, $prevMonthEnd) {
            $q->whereBetween('check_in_date', [$prevMonthStart, $prevMonthEnd])->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        })->sum('total_amount');

        $prevMonthRoomsSold = ReservationRoom::whereHas('reservation', function ($q) use ($prevMonthStart, $prevMonthEnd) {
            $q->whereBetween('check_in_date', [$prevMonthStart, $prevMonthEnd])->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        })->count();

        $prevMonthDays = Carbon::parse($prevMonthStart)->daysInMonth;
        $prevMonthTotalRoomNights = $totalRooms * $prevMonthDays;

        $prevMonthOccupied = NightAudit::whereBetween('audit_date', [$prevMonthStart, $prevMonthEnd])->sum('total_rooms_occupied');
        $prevMonthOccupancy = $prevMonthTotalRoomNights > 0 ? round(($prevMonthOccupied / $prevMonthTotalRoomNights) * 100, 1) : 0;
        $prevMonthAdr = $prevMonthRoomsSold > 0 ? round($prevMonthRevenue / $prevMonthRoomsSold, 2) : 0;
        $prevMonthRevpar = $prevMonthTotalRoomNights > 0 ? round($prevMonthRevenue / $prevMonthTotalRoomNights, 2) : 0;

        $occupancyChange = $prevMonthOccupancy > 0 ? round($monthOccupancy - $prevMonthOccupancy, 1) : null;
        $adrChange = $prevMonthAdr > 0 ? round((($monthAdr - $prevMonthAdr) / $prevMonthAdr) * 100, 1) : null;
        $revparChange = $prevMonthRevpar > 0 ? round((($monthRevpar - $prevMonthRevpar) / $prevMonthRevpar) * 100, 1) : null;

        $recentOccupancy = [];
        $recentDates = [];
        $recentRevenues = [];
        $current = now()->subDays(6);
        while ($current->lte(now())) {
            $dateStr = $current->format('Y-m-d');
            $audit = NightAudit::where('audit_date', $dateStr)->first();
            $recentOccupancy[] = $totalRooms > 0 ? round((($audit->total_rooms_occupied ?? 0) / $totalRooms) * 100, 1) : 0;
            $recentDates[] = $current->format('M d');
            $dayRev = ReservationRoom::whereHas('reservation', function ($q) use ($dateStr) {
                $q->where('check_in_date', $dateStr)->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
            })->sum('total_amount');
            $recentRevenues[] = round($dayRev, 2);
            $current->addDay();
        }

        $activeReservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->count();
        $todayCheckins = Reservation::where('check_in_date', $today)->whereIn('status', ['confirmed', 'checked-in'])->count();
        $todayCheckouts = Reservation::where('check_out_date', $today)->whereIn('status', ['checked-in', 'checked-out'])->count();

        return [
            'today' => [
                'occupancy' => $todayOccupancy,
                'adr' => $todayAdr,
                'revpar' => $todayRevpar,
                'revenue' => round($todayRevenue, 2),
                'rooms_sold' => $todayRoomsSold,
            ],
            'month' => [
                'occupancy' => $monthOccupancy,
                'adr' => $monthAdr,
                'revpar' => $monthRevpar,
                'revenue' => round($monthRevenue, 2),
                'rooms_sold' => $monthRoomsSold,
            ],
            'changes' => [
                'occupancy' => $occupancyChange,
                'adr' => $adrChange,
                'revpar' => $revparChange,
            ],
            'recent_dates' => $recentDates,
            'recent_occupancy' => $recentOccupancy,
            'recent_revenues' => $recentRevenues,
            'total_rooms' => $totalRooms,
            'active_reservations' => $activeReservations,
            'today_checkins' => $todayCheckins,
            'today_checkouts' => $todayCheckouts,
        ];
    }

    private function emptyKpis()
    {
        return [
            'today' => ['occupancy' => 0, 'adr' => 0, 'revpar' => 0, 'revenue' => 0, 'rooms_sold' => 0],
            'month' => ['occupancy' => 0, 'adr' => 0, 'revpar' => 0, 'revenue' => 0, 'rooms_sold' => 0],
            'changes' => ['occupancy' => null, 'adr' => null, 'revpar' => null],
            'recent_dates' => [],
            'recent_occupancy' => [],
            'recent_revenues' => [],
            'total_rooms' => 0,
            'active_reservations' => 0,
            'today_checkins' => 0,
            'today_checkouts' => 0,
        ];
    }
}
