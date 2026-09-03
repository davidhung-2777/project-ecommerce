<?php

namespace App\Services\Payment;

class CodPayment implements PaymentInterface
{
    public function createTransaction(array $order): array
    {
        // COD: no gateway interaction needed
        return [
            'success'      => true,
            'method'       => 'cod',
            'redirect_url' => null,
            'message'      => 'Đơn hàng COD đã được tạo thành công. Vui lòng chuẩn bị tiền mặt khi nhận hàng.',
        ];
    }

    public function handleCallback(array $params): array
    {
        // COD has no callback
        return [
            'success'  => true,
            'order_id' => 0,
            'message'  => 'COD không có callback.',
        ];
    }

    public function checkStatus(array $order): array
    {
        // COD is "paid" only when admin confirms delivery
        $status = $order['payment_status'] ?? 'pending';
        return [
            'status'  => $status === 'paid' ? 'paid' : 'pending',
            'message' => $status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán khi nhận hàng',
        ];
    }

    public function getMethodName(): string
    {
        return 'Thanh toán khi nhận hàng (COD)';
    }
}
