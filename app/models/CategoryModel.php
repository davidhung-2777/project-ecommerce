<?php

namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model
{
    protected string $table = 'categories';

    public function findBySlug(string $slug): array|false
    {
        return $this->findOneWhere('slug = ? AND is_active = 1', [$slug]);
    }

    /**
     * Get top-level categories.
     */
    public function getRootCategories(): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY sort_order ASC'
        );
    }

    /**
     * Get children of a category.
     */
    public function getChildren(int $parentId): array
    {
        return $this->findWhere('parent_id = ? AND is_active = 1', [$parentId], 'sort_order ASC');
    }

    /**
     * Get full tree for mega-menu (2 levels).
     */
    public function getMenuTree(): array
    {
        $roots = $this->getRootCategories();
        foreach ($roots as &$root) {
            $root['children'] = $this->getChildren($root['id']);
        }
        return $roots;
    }

    /**
     * Get siblings (categories with the same parent).
     */
    public function getSiblings(int $parentId): array
    {
        if (!$parentId) return [];
        return $this->findWhere('parent_id = ? AND is_active = 1', [$parentId], 'sort_order ASC');
    }

    /**
     * Get all active categories as flat list.
     */
    public function getAllActive(): array
    {
        return $this->findWhere('is_active = 1', [], 'parent_id ASC, sort_order ASC');
    }
}
