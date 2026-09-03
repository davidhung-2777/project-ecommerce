<?php

namespace App\Services\Payment;

interface PaymentInterface
{
    /**
     * Create a payment transaction and return redirect URL or payment data.
     */
    public function createTransaction(array $order): array;

    /**
     * Handle async callback (IPN/webhook) from payment gateway.
     * Returns ['success' => bool, 'order_id' => int, 'message' => string]
     */
    public function handleCallback(array $params): array;

    /**
     * Check current payment status for an order.
     * Returns ['status' => 'paid'|'pending'|'failed', 'message' => string]
     */
    public function checkStatus(array $order): array;

    /**
     * Get human-readable method name.
     */
    public function getMethodName(): string;
}
