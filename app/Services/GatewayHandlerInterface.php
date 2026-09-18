<?php

namespace App\Services;

interface GatewayHandlerInterface
{
    public function createOrder(float $amount, string $currency, array $meta): array;
    public function verifyPayment(array $paymentData): array;
    public function capturePayment(string $orderId, float $amount): array;
    public function refund(string $paymentId, float $amount, string $reason): array;
    public function getTransactionStatus(string $orderId): array;
}
