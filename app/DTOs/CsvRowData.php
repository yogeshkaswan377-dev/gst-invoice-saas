<?php

namespace App\DTOs;

readonly class CsvRowData
{
    public function __construct(
        public ?string $itemNo,
        public ?string $name,
        public ?string $description,
        public ?string $hsnSacCode,
        public ?float $unitPrice,
        public ?float $gstRate,
        public ?string $unit,
        public ?float $stock,
        public ?string $stockUnit,
        public ?string $stockDeductionType,
        public ?float $consumptionPerPiece,
        public ?float $minimumStock,
        public ?float $sellingPrice,
    ) {}
}
