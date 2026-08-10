<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartSalesInvoice;
use App\Models\PaymentTransaction;
use App\Models\PurchaseOrder;
use App\Models\VehiclePurchaseOrder;
use App\Models\VehicleSalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentTransactionController extends Controller
{
    /**
     * Resolve the target model by alias type and ID.
     */
    private function resolveModel(string $type, int $id)
    {
        switch ($type) {
            case 'vehicle-sales-invoice':
            case 'vehicle_sales_invoice':
            case 'vehicle_sale':
                return VehicleSalesInvoice::with('customer', 'paymentTransactions.createdBy')->findOrFail($id);

            case 'part-sales-invoice':
            case 'part_sales_invoice':
            case 'part_sale':
                return PartSalesInvoice::with('customer', 'paymentTransactions.createdBy')->findOrFail($id);

            case 'vehicle-purchase-order':
            case 'vehicle_purchase_order':
            case 'vehicle_purchase':
                return VehiclePurchaseOrder::with('supplier', 'paymentTransactions.createdBy')->findOrFail($id);

            case 'purchase-order':
            case 'purchase_order':
            case 'part_purchase':
                return PurchaseOrder::with('supplier', 'paymentTransactions.createdBy')->findOrFail($id);

            default:
                abort(404, 'Invalid payment entity type.');
        }
    }

    /**
     * Receive a payment for Sales Invoice or Purchase Order.
     */
    public function receivePayment(Request $request, string $type, int $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        $model = $this->resolveModel($type, $id);
        $amount = floatval($request->input('amount'));

        if ($amount > (float) $model->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount (₹' . number_format($amount, 2) . ') cannot exceed outstanding balance (₹' . number_format($model->balance, 2) . ').'
            ], 422);
        }

        DB::transaction(function () use ($model, $amount, $request) {
            $model->received_amount = (float) $model->received_amount + $amount;
            $model->balance = (float) $model->balance - $amount;

            if (isset($model->current_balance)) {
                $model->current_balance = (float) $model->current_balance - $amount;
            }

            if ($request->filled('payment_mode')) {
                $model->payment_mode = $request->input('payment_mode');
            }

            $model->save();

            PaymentTransaction::create([
                'payable_type' => get_class($model),
                'payable_id' => $model->id,
                'transaction_type' => 'payment',
                'amount' => $amount,
                'payment_mode' => $request->input('payment_mode', $model->payment_mode ?? 'Cash'),
                'reference_no' => $request->input('reference_no'),
                'note' => $request->input('note', 'Payment Received'),
                'created_by' => Auth::id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment of ₹' . number_format($amount, 2) . ' received successfully.',
            'new_received' => number_format($model->received_amount, 2),
            'new_balance' => number_format($model->balance, 2),
        ]);
    }

    /**
     * Rollback (cancel/reverse) a payment for Sales Invoice or Purchase Order.
     */
    public function rollbackPayment(Request $request, string $type, int $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
            'payment_mode' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
        ]);

        $model = $this->resolveModel($type, $id);
        $amount = floatval($request->input('amount'));

        if ($amount > (float) $model->received_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Rollback amount (₹' . number_format($amount, 2) . ') cannot exceed total received amount (₹' . number_format($model->received_amount, 2) . ').'
            ], 422);
        }

        DB::transaction(function () use ($model, $amount, $request) {
            $model->received_amount = max(0, (float) $model->received_amount - $amount);
            $model->balance = (float) $model->balance + $amount;

            if (isset($model->current_balance)) {
                $model->current_balance = (float) $model->current_balance + $amount;
            }

            $model->save();

            PaymentTransaction::create([
                'payable_type' => get_class($model),
                'payable_id' => $model->id,
                'transaction_type' => 'rollback',
                'amount' => $amount,
                'payment_mode' => $request->input('payment_mode', $model->payment_mode ?? 'Cash'),
                'reference_no' => $request->input('reference_no'),
                'note' => $request->input('reason'),
                'created_by' => Auth::id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment rollback of ₹' . number_format($amount, 2) . ' executed successfully.',
            'new_received' => number_format($model->received_amount, 2),
            'new_balance' => number_format($model->balance, 2),
        ]);
    }

    /**
     * Get transaction history for modal display.
     */
    public function history(string $type, int $id)
    {
        $model = $this->resolveModel($type, $id);

        $docNumber = $model->invoice_number ?? $model->po_number ?? $model->order_number ?? ('#' . $model->id);
        $partyName = optional($model->customer)->name ?? $model->customer_name ?? optional($model->supplier)->name ?? 'N/A';
        $totalAmount = (float) ($model->grand_total ?? $model->total_amount ?? 0);
        $receivedAmount = (float) $model->received_amount;
        $balance = (float) $model->balance;

        $history = $model->paymentTransactions->map(function ($tx) {
            return [
                'id' => $tx->id,
                'type' => $tx->transaction_type,
                'amount' => (float) $tx->amount,
                'payment_mode' => $tx->payment_mode ?? 'Cash',
                'reference_no' => $tx->reference_no ?? '-',
                'note' => $tx->note ?? '-',
                'user_name' => optional($tx->createdBy)->name ?? 'System',
                'created_at' => $tx->created_at ? $tx->created_at->format('d-m-Y h:i A') : '',
            ];
        });

        return response()->json([
            'success' => true,
            'doc_number' => $docNumber,
            'party_name' => $partyName,
            'total_amount' => number_format($totalAmount, 2),
            'received_amount' => number_format($receivedAmount, 2),
            'balance' => number_format($balance, 2),
            'raw_received' => $receivedAmount,
            'raw_balance' => $balance,
            'history' => $history,
        ]);
    }

    /**
     * Display Payment Audit Log report.
     */
    public function auditLog(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $type = $request->input('transaction_type', 'all'); // 'all', 'payment', 'rollback'
        $docType = $request->input('doc_type', 'all');
        $search = $request->input('search');

        $query = PaymentTransaction::with(['payable', 'createdBy'])->orderBy('created_at', 'desc');

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        if ($type !== 'all' && in_array($type, ['payment', 'rollback'])) {
            $query->where('transaction_type', $type);
        }
        if ($docType !== 'all') {
            switch ($docType) {
                case 'vehicle_sale':
                    $query->where('payable_type', VehicleSalesInvoice::class);
                    break;
                case 'part_sale':
                    $query->where('payable_type', PartSalesInvoice::class);
                    break;
                case 'vehicle_purchase':
                    $query->where('payable_type', VehiclePurchaseOrder::class);
                    break;
                case 'part_purchase':
                    $query->where('payable_type', PurchaseOrder::class);
                    break;
            }
        }

        if ($search) {
            $escaped = '%' . addcslashes($search, '%_') . '%';
            $query->where(function ($q) use ($escaped) {
                $q->where('note', 'like', $escaped)
                    ->orWhere('payment_mode', 'like', $escaped)
                    ->orWhere('reference_no', 'like', $escaped)
                    ->orWhereHasMorph('payable', [
                        VehicleSalesInvoice::class,
                        PartSalesInvoice::class,
                        VehiclePurchaseOrder::class,
                        PurchaseOrder::class,
                    ], function ($mq, $mType) use ($escaped) {
                        if (in_array($mType, [VehicleSalesInvoice::class, PartSalesInvoice::class])) {
                            $mq->where('invoice_number', 'like', $escaped)
                               ->orWhere('customer_name', 'like', $escaped);
                        } else {
                            $mq->where('po_number', 'like', $escaped)
                               ->orWhere('order_number', 'like', $escaped)
                               ->orWhereHas('supplier', function ($sq) use ($escaped) {
                                   $sq->where('name', 'like', $escaped);
                               });
                        }
                    });
            });
        }

        $transactions = $query->paginate(25)->withQueryString();

        // Summaries
        $totalPaymentsQuery = clone $query;
        $totalPayments = (float) $totalPaymentsQuery->where('transaction_type', 'payment')->sum('amount');

        $totalRollbacksQuery = (clone $query)->where('transaction_type', 'rollback');
        $totalRollbacks = (float) $totalRollbacksQuery->sum('amount');

        return view('admin.reports.payment_audit_log', compact(
            'transactions',
            'fromDate',
            'toDate',
            'type',
            'docType',
            'search',
            'totalPayments',
            'totalRollbacks'
        ));
    }
}
