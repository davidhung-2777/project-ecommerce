<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): array|false
    {
        return $this->findOneWhere('email = ?', [$email]);
    }

    public function findByPhone(string $phone): array|false
    {
        return $this->findOneWhere('phone = ?', [$phone]);
    }

    /**
     * Find by email or phone (for login).
     */
    public function findByCredential(string $credential): array|false
    {
        return $this->db->fetch(
            'SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1',
            [$credential, $credential]
        );
    }

    public function createUser(array $data): int|string
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->create($data);
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function updatePassword(int $userId, string $newPassword): int
    {
        return $this->update($userId, ['password' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12])]);
    }

    public function getActiveCustomers(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "role = 'customer' AND is_active = 1", [], 'created_at DESC');
    }
}
