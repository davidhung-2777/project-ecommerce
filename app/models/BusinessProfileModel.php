<?php

namespace App\Models;

use App\Core\Model;

class BusinessProfileModel extends Model
{
    protected string $table = 'business_profiles';

    public function findByUserId(int $userId): array|false
    {
        return $this->findOneWhere('user_id = ?', [$userId]);
    }

    public function findByTaxCode(string $taxCode): array|false
    {
        return $this->findOneWhere('tax_code = ?', [$taxCode]);
    }

    public function upsert(int $userId, array $data): int|string
    {
        $existing = $this->findByUserId($userId);
        if ($existing) {
            $this->update($existing['id'], $data);
            return $existing['id'];
        }
        $data['user_id'] = $userId;
        return $this->create($data);
    }
}
