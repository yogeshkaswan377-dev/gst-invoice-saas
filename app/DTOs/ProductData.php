<?php

namespace App\DTOs;

readonly class ProductData
{
    public function __construct(
        public int $companyId,
        public ?string $itemNo,
        public string $name,
        public ?string $description,
        public ?string $hsnSacCode,
        public float $unitPrice,
        public float $gstRate,
        public string $unit,
        public bool $isActive,
        public float $stock = 0,
        public string $stockUnit = 'Mtr',
        public string $stockDeductionType = 'Meter',
        public ?float $consumptionPerPiece = null,
        public float $minimumStock = 0,
        public ?float $sellingPrice = null,
    ) {}
}
