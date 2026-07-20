<?php

namespace App\Repositories\Contracts;

use App\Models\StockHistory;
use Illuminate\Database\Eloquent\Collection;

interface StockHistoryRepositoryInterface
{
    public function log(array $data): StockHistory;
    public function getForProduct(int $productId, int $companyId): Collection;
}
