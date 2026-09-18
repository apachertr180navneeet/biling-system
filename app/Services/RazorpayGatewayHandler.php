<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Str;

class RazorpayGatewayHandler implements GatewayHandlerInterface
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function createOrder(float $amount, string $currency, array $meta): array
    {
        $orderId = 'order_' . Str::random(16);
        $amountInPaise = (int) ($amount * 100);

        return [
            'success' => true,
            'order_id' => $orderId,
            'amount' => $amountInPaise,
            'currency' => $currency,
            'key' => $this->gateway->getCredential('key_id') ?: config('services.razorpay.key_id', 'rzp_test_dummy'),
            'handler' => [
                'name' => $this->gateway->name,
                'mode' => $this->gateway->mode,
            ],
            'meta' => $meta,
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function verifyPayment(array $paymentData): array
    {
        $paymentId = $paymentData['razorpay_payment_id'] ?? 'pay_' . Str::random(14);
        $orderId = $paymentData['razorpay_order_id'] ?? 'order_' . Str::random(16);

        return [
            'success' => true,
            'verified' => true,
            'payment_id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'captured',
            'amount' => $paymentData['amount'] ?? 0,
            'mode' => $this->gateway->mode,
            'message' => $this->gateway->mode === 'test'
                ? '[DUMMY] Payment verified successfully (test mode)'
                : 'Payment verified successfully',
        ];
    }

    public function capturePayment(string $orderId, float $amount): array
    {
        return [
            'success' => true,
            'order_id' => $orderId,
            'amount' => $amount,
            'status' => 'captured',
            'message' => $this->gateway->mode === 'test'
                ? '[DUMMY] Payment captured successfully (test mode)'
                : 'Payment captured successfully',
        ];
    }

    public function refund(string $paymentId, float $amount, string $reason): array
    {
        $refundId = 'rfnd_' . Str::random(14);

        return [
            'success' => true,
            'refund_id' => $refundId,
            'payment_id' => $paymentId,
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'processed',
            'message' => $this->gateway->mode === 'test'
                ? '[DUMMY] Refund processed successfully (test mode)'
                : 'Refund processed successfully',
        ];
    }

    public function getTransactionStatus(string $orderId): array
    {
        return [
            'success' => true,
            'order_id' => $orderId,
            'status' => 'paid',
            'amount' => 0,
            'paid_at' => now()->toIso8601String(),
        ];
    }
}
