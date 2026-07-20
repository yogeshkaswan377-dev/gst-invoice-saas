<?php

namespace App\DTOs;

readonly class StockAdjustmentData
{
    public function __construct(
        public int $productId,
        public int $companyId,
        public int $userId,
        public string $adjustmentType, // 'add', 'deduct', 'set'
        public float $quantity,
        public ?string $remarks = null,
    ) {}
}
