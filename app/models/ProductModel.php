<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model
{
    protected string $table = 'products';

    public function findBySlug(string $slug): array|false
    {
        return $this->db->fetch(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.slug = ? AND p.is_active = 1
             LIMIT 1',
            [$slug]
        );
    }

    public function findActiveById(int $id): array|false
    {
        return $this->db->fetch(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = ? AND p.is_active = 1
             LIMIT 1',
            [$id]
        );
    }

    /**
     * Get products with filters and pagination.
     */
    public function getFiltered(array $filters = [], int $page = 1, int $perPage = 16, string $sort = 'newest'): array
    {
        $conditions = ['p.is_active = 1'];
        $params     = [];

        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = ?';
            $params[]     = (int) $filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $conditions[] = 'p.price >= ?';
            $params[]     = (float) $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $conditions[] = 'p.price <= ?';
            $params[]     = (float) $filters['max_price'];
        }

        if (!empty($filters['color'])) {
            $conditions[] = 'p.color LIKE ?';
            $params[]     = '%' . $filters['color'] . '%';
        }

        if (!empty($filters['origin'])) {
            $conditions[] = 'p.origin = ?';
            $params[]     = $filters['origin'];
        }

        if (!empty($filters['is_new'])) {
            $conditions[] = 'p.is_new = 1';
        }

        if (!empty($filters['on_sale'])) {
            $conditions[] = 'p.sale_price IS NOT NULL';
        }

        if (!empty($filters['search'])) {
            $conditions[] = '(p.name LIKE ? OR p.short_desc LIKE ? OR p.sku LIKE ?)';
            $term = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$term, $term, $term]);
        }

        $orderBy = match ($sort) {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name_asc'   => 'p.name ASC',
            'popular'    => 'p.views DESC',
            default      => 'p.created_at DESC',
        };

        $where  = implode(' AND ', $conditions);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM products p WHERE {$where}";
        $total    = (int) ($this->db->fetch($countSql, $params)['COUNT(*)'] ?? 0);

        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE {$where}
                ORDER BY {$orderBy}
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data'         => $this->db->fetchAll($sql, $params),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function getFilteredAdmin(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = '(p.name LIKE ? OR p.short_desc LIKE ? OR p.sku LIKE ?)';
            $term = '%' . $filters['search'] . '%';
            $params = [$term, $term, $term];
        }

        $where = $conditions ? implode(' AND ', $conditions) : '1=1';
        $offset = ($page - 1) * $perPage;
        $total = (int) ($this->db->fetch(
            "SELECT COUNT(*) AS total FROM products p WHERE {$where}",
            $params
        )['total'] ?? 0);

        $products = $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE {$where}
             ORDER BY p.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $products,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function getAdminStats(): array
    {
        return [
            'total' => (int) ($this->db->fetch('SELECT COUNT(*) AS total FROM products')['total'] ?? 0),
            'active' => (int) ($this->db->fetch('SELECT COUNT(*) AS total FROM products WHERE is_active = 1')['total'] ?? 0),
            'low_stock' => (int) ($this->db->fetch('SELECT COUNT(*) AS total FROM products WHERE is_active = 1 AND stock < 5')['total'] ?? 0),
        ];
    }

    public function getRecentAdmin(int $limit = 5): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             ORDER BY p.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function getFeatured(int $limit = 8): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public function getNewArrivals(int $limit = 8): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM products WHERE is_new = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public function getByCategory(int $categoryId, int $limit = 8, int $exclude = 0): array
    {
        $sql    = 'SELECT * FROM products WHERE category_id = ? AND is_active = 1';
        $params = [$categoryId];
        if ($exclude) {
            $sql    .= ' AND id != ?';
            $params[] = $exclude;
        }
        $sql .= ' ORDER BY is_featured DESC, created_at DESC LIMIT ?';
        $params[] = $limit;
        return $this->db->fetchAll($sql, $params);
    }

    public function incrementViews(int $id): void
    {
        $this->db->query('UPDATE products SET views = views + 1 WHERE id = ?', [$id]);
    }

    /**
     * Get effective price based on quantity using price tiers.
     */
    public function getEffectivePrice(int $productId, int $quantity): float
    {
        $tier = $this->db->fetch(
            'SELECT price FROM product_price_tiers
             WHERE product_id = ? AND min_qty <= ?
               AND (max_qty IS NULL OR max_qty >= ?)
             ORDER BY min_qty DESC LIMIT 1',
            [$productId, $quantity, $quantity]
        );

        if ($tier) {
            return (float) $tier['price'];
        }

        $product = $this->find($productId);
        return $product ? (float) ($product['sale_price'] ?? $product['price']) : 0.0;
    }
}
