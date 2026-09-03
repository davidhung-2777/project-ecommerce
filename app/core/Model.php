<?php

namespace App\Core;

abstract class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int|string $id): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1",
            [$id]
        );
    }

    public function findAll(string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit)   $sql .= " LIMIT {$limit}";
        if ($offset)  $sql .= " OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }

    public function findWhere(string $where, array $params = [], string $orderBy = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE {$where}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql, $params);
    }

    public function findOneWhere(string $where, array $params = []): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE {$where} LIMIT 1",
            $params
        );
    }

    public function create(array $data): int|string
    {
        return $this->db->insert($this->table, $data);
    }

    public function update(int|string $id, array $data): int
    {
        return $this->db->update($this->table, $data, "`{$this->primaryKey}` = ?", [$id]);
    }

    public function delete(int|string $id): int
    {
        return $this->db->delete($this->table, "`{$this->primaryKey}` = ?", [$id]);
    }

    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        if ($where) $sql .= " WHERE {$where}";
        $result = $this->db->fetch($sql, $params);
        return (int) ($result['COUNT(*)'] ?? 0);
    }

    public function paginate(int $page, int $perPage, string $where = '', array $params = [], string $orderBy = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count($where, $params);

        $sql = "SELECT * FROM `{$this->table}`";
        if ($where)   $sql .= " WHERE {$where}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data'         => $this->db->fetchAll($sql, $params),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
