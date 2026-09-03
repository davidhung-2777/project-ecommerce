<?php

namespace App\Models;

use App\Core\Model;

class QuoteItemModel extends Model
{
    protected string $table = 'quote_items';

    public function getByQuote(int $quoteId): array
    {
        return $this->findWhere('quote_id = ?', [$quoteId]);
    }

    public function createFromArray(int $quoteId, array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) continue;
            $this->create([
                'quote_id'   => $quoteId,
                'product_id' => (int) $item['product_id'],
                'quantity'   => (int) $item['quantity'],
                'note'       => $item['note'] ?? null,
            ]);
        }
    }
}
