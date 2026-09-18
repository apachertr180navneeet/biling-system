<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Str;

class StripeGatewayHandler implements GatewayHandlerInterface
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function createOrder(float $amount, string $currency, array $meta): array
    {
        $paymentIntentId = 'pi_' . Str::random(24);
        $clientSecret = 'cs_' . Str::random(24) . '_secret_' . Str::random(16);
        $amountInCents = (int) ($amount * 100);

        return [
            'success' => true,
            'payment_intent_id' => $paymentIntentId,
            'client_secret' => $clientSecret,
            'amount' => $amountInCents,
            'currency' => strtolower($currency),
            'key' => $this->gateway->getCredential('publishable_key') ?: config('services.stripe.publishable_key', 'pk_test_dummy'),
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
        $paymentIntentId = $paymentData['payment_intent_id'] ?? 'pi_' . Str::random(24);

        return [
            'success' => true,
            'verified' => true,
            'payment_intent_id' => $paymentIntentId,
            'status' => 'succeeded',
            'amount' => $paymentData['amount'] ?? 0,
            'mode' => $this->gateway->mode,
            'message' => $this->gateway->mode === 'test'
                ? '[DUMMY] Payment confirmed successfully (test mode)'
                : 'Payment confirmed successfully',
        ];
    }

    public function capturePayment(string $orderId, float $amount): array
    {
        return [
            'success' => true,
            'payment_intent_id' => $orderId,
            'amount' => $amount,
            'status' => 'succeeded',
            'message' => $this->gateway->mode === 'test'
                ? '[DUMMY] Payment captured successfully (test mode)'
                : 'Payment captured successfully',
        ];
    }

    public function refund(string $paymentId, float $amount, string $reason): array
    {
        $refundId = 're_' . Str::random(24);

        return [
            'success' => true,
            'refund_id' => $refundId,
            'payment_intent_id' => $paymentId,
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'succeeded',
            'message' => $this->gateway->mode === 'test'
                ? '[DUMMY] Refund processed successfully (test mode)'
                : 'Refund processed successfully',
        ];
    }

    public function getTransactionStatus(string $orderId): array
    {
        return [
            'success' => true,
            'payment_intent_id' => $orderId,
            'status' => 'succeeded',
            'amount' => 0,
            'paid_at' => now()->toIso8601String(),
        ];
    }
}
