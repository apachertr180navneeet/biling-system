<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\CheckOut;
use App\Models\RestaurantOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PrinterService
{
    public function printInvoice(CheckOut $checkout, Device $device): bool
    {
        try {
            $reservation = $checkout->reservation()->with(['guest', 'rooms.roomType', 'payments'])->first();
            $hotel = $checkout->hotel;

            $data = [
                'api_key' => $device->api_key,
                'type' => 'invoice',
                'header' => [
                    'hotel_name' => $hotel->name ?? config('app.name'),
                    'hotel_address' => $hotel->address ?? '',
                    'hotel_phone' => $hotel->phone ?? '',
                ],
                'guest_name' => $reservation->guest->full_name ?? 'Guest',
                'reservation_number' => $reservation->reservation_number ?? '',
                'room_number' => $reservation->rooms->first()->room_number ?? '',
                'check_in_date' => $reservation->check_in_date?->format('d M Y'),
                'check_out_date' => $reservation->actual_check_out?->format('d M Y'),
                'line_items' => $this->prepareInvoiceLines($reservation),
                'total' => number_format($checkout->final_bill_amount, 2),
                'payments' => number_format($checkout->total_payments, 2),
                'balance' => number_format($checkout->balance_due, 2),
                'footer' => 'Thank you for staying with us!',
            ];

            $response = Http::timeout(10)->post(
                "http://{$device->ip_address}:{$device->port}/api/print",
                $data
            );

            if ($response->successful()) {
                DeviceLog::create([
                    'device_id' => $device->id,
                    'event_type' => 'print_job',
                    'user_id' => auth()->id(),
                    'reservation_id' => $reservation->id,
                    'data' => ['type' => 'invoice', 'reservation_number' => $reservation->reservation_number],
                    'status' => 'success',
                    'message' => "Invoice printed for {$reservation->reservation_number}",
                ]);
                return true;
            }

            throw new Exception('Printer returned error');
        } catch (Exception $e) {
            Log::error("Print invoice failed on device {$device->id}: " . $e->getMessage());
            DeviceLog::create([
                'device_id' => $device->id,
                'event_type' => 'print_job',
                'user_id' => auth()->id(),
                'reservation_id' => $checkout->reservation_id,
                'data' => ['type' => 'invoice', 'error' => $e->getMessage()],
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function printKOT(RestaurantOrder $order, Device $device): bool
    {
        try {
            $order->load(['table', 'items.menuItem', 'guest']);

            $data = [
                'api_key' => $device->api_key,
                'type' => 'kot',
                'header' => [
                    'restaurant' => 'Restaurant',
                    'order_number' => $order->order_number,
                ],
                'table_number' => $order->table?->table_number ?? 'N/A',
                'guest_name' => $order->guest?->full_name ?? 'Walk-in',
                'room_number' => $order->room_number ?? '',
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->menuItem->name ?? '',
                        'quantity' => $item->quantity,
                        'price' => number_format($item->unit_price, 2),
                        'notes' => $item->notes ?? '',
                    ];
                })->toArray(),
                'total' => number_format($order->total_amount, 2),
                'timestamp' => now()->format('d M Y, h:i A'),
            ];

            $response = Http::timeout(10)->post(
                "http://{$device->ip_address}:{$device->port}/api/print",
                $data
            );

            if ($response->successful()) {
                DeviceLog::create([
                    'device_id' => $device->id,
                    'event_type' => 'print_job',
                    'user_id' => auth()->id(),
                    'data' => ['type' => 'kot', 'order_number' => $order->order_number],
                    'status' => 'success',
                    'message' => "KOT printed for order {$order->order_number}",
                ]);
                return true;
            }

            throw new Exception('Printer returned error');
        } catch (Exception $e) {
            Log::error("Print KOT failed on device {$device->id}: " . $e->getMessage());
            DeviceLog::create([
                'device_id' => $device->id,
                'event_type' => 'print_job',
                'user_id' => auth()->id(),
                'data' => ['type' => 'kot', 'error' => $e->getMessage()],
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function printReceipt(array $data, Device $device): bool
    {
        try {
            $payload = array_merge($data, [
                'api_key' => $device->api_key,
                'type' => 'receipt',
            ]);

            $response = Http::timeout(10)->post(
                "http://{$device->ip_address}:{$device->port}/api/print",
                $payload
            );

            if ($response->successful()) {
                DeviceLog::create([
                    'device_id' => $device->id,
                    'event_type' => 'print_job',
                    'user_id' => auth()->id(),
                    'data' => ['type' => 'receipt'],
                    'status' => 'success',
                    'message' => 'Receipt printed',
                ]);
                return true;
            }

            throw new Exception('Printer returned error');
        } catch (Exception $e) {
            Log::error("Print receipt failed on device {$device->id}: " . $e->getMessage());
            return false;
        }
    }

    public function testPrint(Device $device): bool
    {
        try {
            $response = Http::timeout(5)->post("http://{$device->ip_address}:{$device->port}/api/print", [
                'api_key' => $device->api_key,
                'type' => 'test',
                'message' => 'Mehmaan ERP - Test Print',
                'timestamp' => now()->format('d M Y, h:i:s A'),
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error("Test print failed on device {$device->id}: " . $e->getMessage());
            return false;
        }
    }

    private function prepareInvoiceLines($reservation): array
    {
        $lines = [];

        foreach ($reservation->rooms as $room) {
            $nights = $reservation->check_in_date && $reservation->actual_check_out
                ? $reservation->check_in_date->diffInDays($reservation->actual_check_out)
                : 1;

            $lines[] = [
                'description' => "Room: {$room->room_number} ({$room->roomType->name ?? ''})",
                'qty' => $nights,
                'rate' => number_format($room->pivot->rate ?? 0, 2),
                'amount' => number_format(($room->pivot->rate ?? 0) * $nights, 2),
            ];
        }

        foreach ($reservation->payments as $payment) {
            $lines[] = [
                'description' => "Payment: {$payment->payment_method}",
                'qty' => 1,
                'rate' => number_format($payment->amount, 2),
                'amount' => number_format($payment->amount, 2),
            ];
        }

        return $lines;
    }
}
