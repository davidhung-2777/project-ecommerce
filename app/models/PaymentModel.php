<?php

namespace App\Models;

use App\Core\Model;

class PaymentModel extends Model
{
    protected string $table = 'payments';

    public function findByTransactionId(string $txnId): array|false
    {
        return $this->findOneWhere('transaction_id = ?', [$txnId]);
    }

    public function findByOrder(int $orderId): array|false
    {
        return $this->db->fetch(
            'SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1',
            [$orderId]
        );
    }

    public function createPayment(int $orderId, string $method, float $amount, string $txnId = ''): int|string
    {
        return $this->create([
            'order_id'       => $orderId,
            'transaction_id' => $txnId ?: 'TXN' . time() . rand(1000, 9999),
            'method'         => $method,
            'amount'         => $amount,
            'status'         => 'pending',
        ]);
    }

    public function markPaid(int $paymentId, array $gatewayResponse = []): void
    {
        $this->update($paymentId, [
            'status'           => 'paid',
            'gateway_response' => json_encode($gatewayResponse),
            'paid_at'          => date('Y-m-d H:i:s'),
        ]);
    }

    public function markFailed(int $paymentId, array $gatewayResponse = []): void
    {
        $this->update($paymentId, [
            'status'           => 'failed',
            'gateway_response' => json_encode($gatewayResponse),
        ]);
    }

    public function getPendingBankTransfers(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, o.order_number, o.total_amount, o.shipping_name
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             WHERE p.method = 'bank_transfer' AND p.status = 'pending'
             ORDER BY p.created_at DESC"
        );
    }
}
