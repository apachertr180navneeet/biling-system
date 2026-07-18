<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\HsnSacMaster;

class GstCalculator
{
    public function calculateForVehicle(float $price, bool $isGst, ?Customer $customer): array
    {
        if (!$isGst) {
            return $this->zeroTax($price);
        }

        $gstRate = 28;
        $cessRate = 0;

        $gstType = $this->determineGstType($customer);
        $gstAmount = round($price * $gstRate / 100, 2);
        $cessAmount = round($price * $cessRate / 100, 2);
        $total = $price + $gstAmount + $cessAmount;
        $roundOff = round($total) - $total;
        $grandTotal = round($total);

        $cgstAmount = 0;
        $sgstAmount = 0;
        $igstAmount = 0;

        if ($gstType === 'cgst_sgst') {
            $cgstAmount = round($gstAmount / 2, 2);
            $sgstAmount = round($gstAmount - $cgstAmount, 2);
        } else {
            $igstAmount = $gstAmount;
        }

        return compact('gstRate', 'cessRate', 'gstType', 'gstAmount', 'cessAmount', 'total', 'roundOff', 'grandTotal', 'cgstAmount', 'sgstAmount', 'igstAmount');
    }

    public function calculateForItems(array $items, bool $isGst, ?Customer $customer): array
    {
        $subtotal = 0;
        $totalGst = 0;
        $totalCess = 0;
        $gstType = $isGst ? $this->determineGstType($customer) : null;
        $calculatedItems = [];

        foreach ($items as $item) {
            $qty = $item['quantity'] ?? 1;
            $price = $item['unit_price'] ?? 0;
            $gstRate = $isGst ? ($item['gst_rate'] ?? 0) : 0;
            $cessRate = $isGst ? ($item['cess_rate'] ?? 0) : 0;
            $taxableValue = $qty * $price;
            $subtotal += $taxableValue;
            $gstAmount = round($taxableValue * $gstRate / 100, 2);
            $cessAmount = round($taxableValue * $cessRate / 100, 2);
            $total = $taxableValue + $gstAmount + $cessAmount;
            $totalGst += $gstAmount;
            $totalCess += $cessAmount;

            $cgstAmount = 0;
            $sgstAmount = 0;
            $igstAmount = 0;

            if ($gstType === 'cgst_sgst') {
                $cgstAmount = round($gstAmount / 2, 2);
                $sgstAmount = round($gstAmount - $cgstAmount, 2);
            } else {
                $igstAmount = $gstAmount;
            }

            $calculatedItems[] = [
                'description' => $item['description'] ?? '',
                'spare_part_id' => $item['spare_part_id'] ?? null,
                'quantity' => $qty,
                'unit_price' => $price,
                'gst_rate' => $gstRate,
                'cess_rate' => $cessRate,
                'taxable_value' => $taxableValue,
                'gst_amount' => $gstAmount,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'igst_amount' => $igstAmount,
                'cess_amount' => $cessAmount,
                'total' => $total,
            ];
        }

        $totalAmount = $subtotal + $totalGst + $totalCess;
        $roundOff = round($totalAmount) - $totalAmount;
        $grandTotal = round($totalAmount);

        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;

        if ($gstType === 'cgst_sgst') {
            $totalCgst = round($totalGst / 2, 2);
            $totalSgst = round($totalGst - $totalCgst, 2);
        } else {
            $totalIgst = $totalGst;
        }

        return compact('calculatedItems', 'subtotal', 'totalGst', 'totalCess', 'totalAmount', 'roundOff', 'grandTotal', 'gstType', 'totalCgst', 'totalSgst', 'totalIgst');
    }

    private function determineGstType(?Customer $customer): string
    {
        $sellerState = config('app.seller_state', 'Delhi');
        if (!$customer || $customer->state === $sellerState) {
            return 'cgst_sgst';
        }
        return 'igst';
    }

    private function zeroTax(float $price): array
    {
        $gstType = null;
        $gstRate = 0;
        $cessRate = 0;
        $gstAmount = 0;
        $cessAmount = 0;
        $total = $price;
        $roundOff = round($total) - $total;
        $grandTotal = round($total);
        $cgstAmount = 0;
        $sgstAmount = 0;
        $igstAmount = 0;
        return compact('gstRate', 'cessRate', 'gstType', 'gstAmount', 'cessAmount', 'total', 'roundOff', 'grandTotal', 'cgstAmount', 'sgstAmount', 'igstAmount');
    }
}
