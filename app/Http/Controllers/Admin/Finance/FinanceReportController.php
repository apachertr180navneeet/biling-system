<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\NightAudit;
use Carbon\Carbon;

class FinanceReportController extends Controller
{
    public function dashboard(Request $request)
    {
        $hotelId = $request->get('hotel_id');
        $from = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to_date', now()->format('Y-m-d'));

        $hotels = Hotel::where('status', 'active')->get();
        $hotel = $hotelId ? Hotel::find($hotelId) : $hotels->first();

        $totalRooms = $hotel ? Room::where('hotel_id', $hotel->id)->where('status', 'active')->count() : Room::where('status', 'active')->count();

        $occupancyData = $this->getOccupancyData($hotel, $from, $to, $totalRooms);
        $revenueData = $this->getRevenueData($hotel, $from, $to);
        $roomTypeBreakdown = $this->getRoomTypeBreakdown($hotel, $from, $to);
        $bookingSourceData = $this->getBookingSourceData($hotel, $from, $to);

        $totalRoomNights = $occupancyData['sum_occupied'];
        $totalRevenue = $revenueData['total_revenue'];
        $totalSoldRooms = $revenueData['total_rooms_sold'];

        $occupancyRate = $totalRooms > 0 ? round(($totalRoomNights / ($totalRooms * $this->daysBetween($from, $to))) * 100, 1) : 0;
        $adr = $totalSoldRooms > 0 ? round($totalRevenue / $totalSoldRooms, 2) : 0;
        $revpar = $totalRooms > 0 ? round($totalRevenue / ($totalRooms * $this->daysBetween($from, $to)), 2) : 0;

        $prevFrom = Carbon::parse($from)->subDays($this->daysBetween($from, $to))->format('Y-m-d');
        $prevTo = Carbon::parse($from)->subDay()->format('Y-m-d');
        $prevOccupancy = $this->getOccupancyData($hotel, $prevFrom, $prevTo, $totalRooms);
        $prevRevenue = $this->getRevenueData($hotel, $prevFrom, $prevTo);
        $prevDays = $this->daysBetween($prevFrom, $prevTo);
        $prevOccupancyRate = $totalRooms > 0 ? round(($prevOccupancy['sum_occupied'] / ($totalRooms * $prevDays)) * 100, 1) : 0;
        $prevAdr = $prevRevenue['total_rooms_sold'] > 0 ? round($prevRevenue['total_revenue'] / $prevRevenue['total_rooms_sold'], 2) : 0;
        $prevRevpar = $totalRooms > 0 ? round($prevRevenue['total_revenue'] / ($totalRooms * $prevDays), 2) : 0;

        $occupancyChange = $prevOccupancyRate > 0 ? round($occupancyRate - $prevOccupancyRate, 1) : null;
        $adrChange = $prevAdr > 0 ? round((($adr - $prevAdr) / $prevAdr) * 100, 1) : null;
        $revparChange = $prevRevpar > 0 ? round((($revpar - $prevRevpar) / $prevRevpar) * 100, 1) : null;

        $totalReservations = $this->getReservationCount($hotel, $from, $to);
        $avgNights = $this->getAvgNights($hotel, $from, $to);
        $avgGuests = $this->getAvgGuests($hotel, $from, $to);
        $cancelRate = $this->getCancellationRate($hotel, $from, $to);

        return view('admin.finance.reports.dashboard', compact(
            'hotels', 'hotel', 'from', 'to', 'totalRooms',
            'occupancyRate', 'adr', 'revpar', 'occupancyChange', 'adrChange', 'revparChange',
            'occupancyData', 'revenueData', 'roomTypeBreakdown', 'bookingSourceData',
            'totalReservations', 'avgNights', 'avgGuests', 'cancelRate', 'totalRevenue',
            'totalSoldRooms', 'totalRoomNights'
        ));
    }

    private function getOccupancyData($hotel, $from, $to, $totalRooms)
    {
        $audits = NightAudit::whereBetween('audit_date', [$from, $to]);
        if ($hotel) {
            $audits->where('hotel_id', $hotel->id);
        }
        $audits = $audits->orderBy('audit_date')->get();

        $dates = [];
        $occupied = [];
        $available = [];
        $sumOccupied = 0;

        $current = Carbon::parse($from);
        $end = Carbon::parse($to);

        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $audit = $audits->first(fn($a) => $a->audit_date->format('Y-m-d') === $dateStr);
            $occ = $audit->total_rooms_occupied ?? 0;
            $sumOccupied += $occ;
            $dates[] = $current->format('M d');
            $occupied[] = $occ;
            $available[] = $totalRooms;
            $current->addDay();
        }

        return ['dates' => $dates, 'occupied' => $occupied, 'available' => $available, 'sum_occupied' => $sumOccupied];
    }

    private function getRevenueData($hotel, $from, $to)
    {
        $reservations = Reservation::whereBetween('check_in_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        if ($hotel) {
            $reservations->where('hotel_id', $hotel->id);
        }

        $roomRevenue = ReservationRoom::whereHas('reservation', function ($q) use ($hotel, $from, $to) {
            $q->whereBetween('check_in_date', [$from, $to])->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
            if ($hotel) {
                $q->where('hotel_id', $hotel->id);
            }
        })->sum('total_amount');

        $totalRoomsSold = ReservationRoom::whereHas('reservation', function ($q) use ($hotel, $from, $to) {
            $q->whereBetween('check_in_date', [$from, $to])->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
            if ($hotel) {
                $q->where('hotel_id', $hotel->id);
            }
        })->count();

        $dailyRevenue = [];
        $current = Carbon::parse($from);
        $end = Carbon::parse($to);
        $dates = [];
        $revenues = [];

        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $dayRev = ReservationRoom::whereHas('reservation', function ($q) use ($hotel, $dateStr) {
                $q->where('check_in_date', $dateStr)->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
                if ($hotel) {
                    $q->where('hotel_id', $hotel->id);
                }
            })->sum('total_amount');

            $dates[] = $current->format('M d');
            $revenues[] = round($dayRev, 2);
            $current->addDay();
        }

        return [
            'total_revenue' => $roomRevenue,
            'total_rooms_sold' => $totalRoomsSold,
            'dates' => $dates,
            'revenues' => $revenues,
        ];
    }

    private function getRoomTypeBreakdown($hotel, $from, $to)
    {
        $query = ReservationRoom::selectRaw('room_type_id, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereHas('reservation', function ($q) use ($hotel, $from, $to) {
                $q->whereBetween('check_in_date', [$from, $to])->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
                if ($hotel) {
                    $q->where('hotel_id', $hotel->id);
                }
            })
            ->groupBy('room_type_id');

        return $query->get()->map(function ($item) {
            $item->name = $item->roomType->name ?? 'Unknown';
            $item->revenue = round($item->revenue, 2);
            return $item;
        });
    }

    private function getBookingSourceData($hotel, $from, $to)
    {
        $query = Reservation::selectRaw('booking_source, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('check_in_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        if ($hotel) {
            $query->where('hotel_id', $hotel->id);
        }

        return $query->groupBy('booking_source')->get()->map(function ($item) {
            $item->revenue = round($item->revenue, 2);
            return $item;
        });
    }

    private function daysBetween($from, $to)
    {
        return max(1, Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1);
    }

    private function getReservationCount($hotel, $from, $to)
    {
        $q = Reservation::whereBetween('check_in_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        if ($hotel) {
            $q->where('hotel_id', $hotel->id);
        }
        return $q->count();
    }

    private function getAvgNights($hotel, $from, $to)
    {
        $reservations = Reservation::whereBetween('check_in_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        if ($hotel) {
            $reservations->where('hotel_id', $hotel->id);
        }
        $reservations = $reservations->get();
        if ($reservations->isEmpty()) return 0;
        $totalNights = $reservations->sum(fn($r) => $r->check_in_date->diffInDays($r->check_out_date));
        return round($totalNights / $reservations->count(), 1);
    }

    private function getAvgGuests($hotel, $from, $to)
    {
        $q = Reservation::whereBetween('check_in_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'checked-in', 'checked-out']);
        if ($hotel) {
            $q->where('hotel_id', $hotel->id);
        }
        $reservations = $q->get();
        if ($reservations->isEmpty()) return 0;
        $totalGuests = $reservations->sum(fn($r) => $r->adults + $r->children);
        return round($totalGuests / $reservations->count(), 1);
    }

    private function getCancellationRate($hotel, $from, $to)
    {
        $q = Reservation::whereBetween('check_in_date', [$from, $to]);
        if ($hotel) {
            $q->where('hotel_id', $hotel->id);
        }
        $total = $q->count();
        if ($total == 0) return 0;
        $cancelled = (clone $q)->where('status', 'cancelled')->count();
        return round(($cancelled / $total) * 100, 1);
    }
}
