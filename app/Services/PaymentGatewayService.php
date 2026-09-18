<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Exception;

class PaymentGatewayService
{
    public function createOrder(PaymentGateway $gateway, float $amount, string $currency = 'INR', array $meta = []): array
    {
        $handler = $this->getHandler($gateway);

        return $handler->createOrder($amount, $currency, $meta);
    }

    public function verifyPayment(PaymentGateway $gateway, array $paymentData): array
    {
        $handler = $this->getHandler($gateway);

        return $handler->verifyPayment($paymentData);
    }

    public function capturePayment(PaymentGateway $gateway, string $orderId, float $amount): array
    {
        $handler = $this->getHandler($gateway);

        return $handler->capturePayment($orderId, $amount);
    }

    public function refund(PaymentGateway $gateway, string $paymentId, float $amount, string $reason = ''): array
    {
        $handler = $this->getHandler($gateway);

        return $handler->refund($paymentId, $amount, $reason);
    }

    public function getTransactionStatus(PaymentGateway $gateway, string $orderId): array
    {
        $handler = $this->getHandler($gateway);

        return $handler->getTransactionStatus($orderId);
    }

    private function getHandler(PaymentGateway $gateway): GatewayHandlerInterface
    {
        return match ($gateway->slug) {
            'razorpay' => new RazorpayGatewayHandler($gateway),
            'stripe' => new StripeGatewayHandler($gateway),
            default => throw new Exception("Unsupported payment gateway: {$gateway->slug}"),
        };
    }
}
