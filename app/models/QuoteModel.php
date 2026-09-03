<?php

namespace App\Models;

use App\Core\Model;

class QuoteModel extends Model
{
    protected string $table = 'quotes';

    public function generateQuoteNumber(): string
    {
        return 'BG' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    public function findByQuoteNumber(string $qn): array|false
    {
        return $this->findOneWhere('quote_number = ?', [$qn]);
    }

    public function getByUser(int $userId): array
    {
        return $this->findWhere('user_id = ?', [$userId], 'created_at DESC');
    }

    public function getFullQuote(int $quoteId): array|false
    {
        $quote = $this->find($quoteId);
        if (!$quote) return false;

        $quote['items'] = $this->db->fetchAll(
            'SELECT qi.*, p.name AS product_name, p.thumbnail, p.sku, p.price AS list_price
             FROM quote_items qi
             JOIN products p ON qi.product_id = p.id
             WHERE qi.quote_id = ?',
            [$quoteId]
        );

        $quote['user'] = $this->db->fetch(
            'SELECT u.*, bp.company_name, bp.tax_code
             FROM users u
             LEFT JOIN business_profiles bp ON u.id = bp.user_id
             WHERE u.id = ?',
            [$quote['user_id']]
        );

        return $quote;
    }

    public function getAdminList(string $status = '', int $page = 1, int $perPage = 20): array
    {
        $where  = $status ? 'q.status = ?' : '1=1';
        $params = $status ? [$status] : [];
        $offset = ($page - 1) * $perPage;

        $total = (int) ($this->db->fetch("SELECT COUNT(*) FROM quotes q WHERE {$where}", $params)['COUNT(*)'] ?? 0);

        $sql = "SELECT q.*, u.name AS customer_name, u.email AS customer_email, bp.company_name
                FROM quotes q
                JOIN users u ON q.user_id = u.id
                LEFT JOIN business_profiles bp ON u.id = bp.user_id
                WHERE {$where}
                ORDER BY q.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data'         => $this->db->fetchAll($sql, $params),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
