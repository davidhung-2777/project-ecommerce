<?php

namespace App\Models;

use App\Core\Model;

class OrderModel extends Model
{
    protected string $table = 'orders';

    public function findByOrderNumber(string $orderNumber): array|false
    {
        return $this->findOneWhere('order_number = ?', [$orderNumber]);
    }

    /**
     * Find order by order code (alias for findByOrderNumber)
     * 
     * @param string $orderCode
     * @return array|false
     */
    public function findByOrderCode(string $orderCode): array|false
    {
        return $this->findByOrderNumber($orderCode);
    }

    /**
     * Update order status
     * 
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        return $this->update($orderId, ['status' => $status]);
    }

    public function updatePaymentStatus(int $orderId, string $paymentStatus, ?string $paidAt = null): bool
    {
        $data = ['payment_status' => $paymentStatus];
        if ($paidAt !== null && $this->hasColumn('paid_at')) {
            $data['paid_at'] = $paidAt;
        }

        return $this->update($orderId, $data);
    }

    private function hasColumn(string $column): bool
    {
        $result = $this->db->fetch(
            'SELECT COUNT(*) AS column_count
             FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?',
            [$this->table, $column]
        );

        return (int) ($result['column_count'] ?? 0) > 0;
    }

    public function generateOrderNumber(): string
    {
        return 'DN' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    public function getByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $total = (int) ($this->db->fetch(
            'SELECT COUNT(*) AS total FROM orders WHERE user_id = ?',
            [$userId]
        )['total'] ?? 0);

        return [
            'data'         => $this->db->fetchAll(
                'SELECT o.*, COALESCE(SUM(od.quantity), 0) AS item_count
                 FROM orders o
                 LEFT JOIN order_details od ON od.order_id = o.id
                 WHERE o.user_id = ?
                 GROUP BY o.id
                 ORDER BY o.created_at DESC
                 LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset,
                [$userId]
            ),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /**
     * Return purchase metrics for the customer detail and dashboard views.
     */
    public function getPurchaseStats(int $userId): array
    {
        $stats = $this->db->fetch(
            "SELECT
                COUNT(*) AS total_orders,
                COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN order_quantity ELSE 0 END), 0) AS total_products,
                COALESCE(SUM(CASE WHEN status IN ('confirmed', 'processing', 'shipped', 'delivered') THEN total_amount ELSE 0 END), 0) AS total_spent
             FROM (
                SELECT o.id, o.status, o.total_amount,
                       COALESCE(SUM(od.quantity), 0) AS order_quantity
                FROM orders o
                LEFT JOIN order_details od ON od.order_id = o.id
                WHERE o.user_id = ?
                GROUP BY o.id
             ) customer_orders",
            [$userId]
        ) ?: [];

        return [
            'total_orders'   => (int) ($stats['total_orders'] ?? 0),
            'total_products' => (int) ($stats['total_products'] ?? 0),
            'total_spent'    => (float) ($stats['total_spent'] ?? 0),
        ];
    }

    public function getTotalSpent(int $userId): float
    {
        return (float) ($this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) AS total
             FROM orders
             WHERE user_id = ? AND status IN ('confirmed', 'processing', 'shipped', 'delivered')",
            [$userId]
        )['total'] ?? 0);
    }

    /**
     * Get order with full details (items, payment).
     */
    public function getFullOrder(int $orderId): array|false
    {
        $order = $this->find($orderId);
        if (!$order) return false;

        $order['items'] = $this->db->fetchAll(
            'SELECT * FROM order_details WHERE order_id = ?',
            [$orderId]
        );

        $order['payment'] = $this->db->fetch(
            'SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1',
            [$orderId]
        );

        return $order;
    }

    public function getRecentOrders(int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT o.*, u.name AS customer_name, u.email AS customer_email
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getFilteredAdmin(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'o.status = ?';
            $params[]     = $filters['status'];
        }
        if (!empty($filters['payment_method'])) {
            $conditions[] = 'o.payment_method = ?';
            $params[]     = $filters['payment_method'];
        }
        if (!empty($filters['search'])) {
            $conditions[] = '(o.order_number LIKE ? OR o.shipping_name LIKE ? OR o.shipping_phone LIKE ?)';
            $term = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$term, $term, $term]);
        }

        $where  = $conditions ? implode(' AND ', $conditions) : '1=1';
        $offset = ($page - 1) * $perPage;

        $total = (int) ($this->db->fetch(
            "SELECT COUNT(*) FROM orders o WHERE {$where}", $params
        )['COUNT(*)'] ?? 0);

        $sql = "SELECT o.*, u.name AS customer_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE {$where}
                ORDER BY o.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data'         => $this->db->fetchAll($sql, $params),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function getRevenueStats(): array
    {
        return [
            'today'   => $this->getRevenue('TODAY'),
            'week'    => $this->getRevenue('WEEK'),
            'month'   => $this->getRevenue('MONTH'),
        ];
    }

    private function getRevenue(string $period): float
    {
        $where = match($period) {
            'TODAY' => "DATE(created_at) = CURDATE()",
            'WEEK'  => "YEARWEEK(created_at, 1) = YEARWEEK(NOW(), 1)",
            'MONTH' => "YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())",
            default => '1=1',
        };
        $result = $this->db->fetch(
            "SELECT SUM(total_amount) AS total FROM orders WHERE payment_status = 'paid' AND {$where}"
        );
        return (float) ($result['total'] ?? 0);
    }
}
