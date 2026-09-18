<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Str;

class EInvoiceService
{
    public function generateIRN(array $invoiceData): array
    {
        $company = Company::first();

        if (!$company || !$company->is_gst_registered || empty($company->gstin)) {
            return [
                'success' => false,
                'error' => 'Company GSTIN not configured. Please update Company Profile.',
            ];
        }

        $irn = hash('sha256', $company->gstin . $invoiceData['invoice_number'] . $invoiceData['invoice_date']);
        $irn = substr($irn, 0, 64);

        $ackNo = 'ACK' . now()->format('YmdHis') . Str::random(4);
        $ackDate = now()->format('d-m-Y H:i:s');

        $qrPayload = base64_encode(json_encode([
            'ver' => '1.1',
            'txn' => Str::random(32),
            'irn' => $irn,
            'typ' => 'inv',
            'rgp' => 'N',
        ]));

        return [
            'success' => true,
            'mode' => 'test',
            'irn' => $irn,
            'ack_no' => $ackNo,
            'ack_date' => $ackDate,
            'einvoicestatus' => 'Y',
            'qr_code' => 'data:image/png;base64,' . base64_encode($qrPayload),
            'gstin' => $company->gstin,
            'message' => '[DUMMY] e-Invoice generated successfully via IRP (Test Mode)',
        ];
    }

    public function getIRNStatus(string $irn): array
    {
        return [
            'success' => true,
            'irn' => $irn,
            'einvoicestatus' => 'Y',
            'gstin' => '27AADCB2230M1ZT',
            'irn_date' => now()->subMinutes(5)->format('d-m-Y H:i:s'),
            'message' => '[DUMMY] IRN status retrieved (Test Mode)',
        ];
    }

    public function cancelIRN(string $irn, string $reason = ''): array
    {
        return [
            'success' => true,
            'irn' => $irn,
            'cancelstatus' => 'Y',
            'cancel_date' => now()->format('d-m-Y H:i:s'),
            'reason' => $reason ?: 'Data Entry Mistake',
            'message' => '[DUMMY] IRN cancelled successfully (Test Mode)',
        ];
    }

    public function generateEWayBill(array $ewayData): array
    {
        $ewayBillNo = 'EWB' . now()->format('Ymd') . Str::random(6);
        $ewayBillDate = now()->format('d-m-Y H:i:s');
        $validUpto = now()->addDays(1)->format('d-m-Y H:i:s');

        return [
            'success' => true,
            'mode' => 'test',
            'eway_bill_no' => $ewayBillNo,
            'eway_bill_date' => $ewayBillDate,
            'valid_upto' => $validUpto,
            'gstin_from' => $ewayData['gstin_from'] ?? '',
            'gstin_to' => $ewayData['gstin_to'] ?? '',
            'total_value' => $ewayData['total_value'] ?? 0,
            'message' => '[DUMMY] e-Way Bill generated successfully (Test Mode)',
        ];
    }
}
