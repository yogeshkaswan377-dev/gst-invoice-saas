<?php

namespace App\Repositories;

use App\Models\StockHistory;
use App\Repositories\Contracts\StockHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StockHistoryRepository implements StockHistoryRepositoryInterface
{
    public function log(array $data): StockHistory
    {
        return StockHistory::create($data);
    }

    public function getForProduct(int $productId, int $companyId): Collection
    {
        return StockHistory::where('product_id', $productId)
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
