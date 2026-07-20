<?php

namespace App\Services;

use App\DTOs\StockAdjustmentData;
use App\Models\Product;
use App\Repositories\Contracts\StockHistoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function __construct(
        private StockHistoryRepositoryInterface $historyRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Adjust stock and log history.
     * Returns updated product.
     */
    public function adjustStock(StockAdjustmentData $data): Product
    {
        $product = $this->productRepository->findByIdForCompany($data->productId, $data->companyId);
        if (!$product) {
            throw new \Exception('Product not found');
        }

        $previousStock = $product->stock;
        $newStock = match ($data->adjustmentType) {
            'add' => $previousStock + $data->quantity,
            'deduct' => $previousStock - $data->quantity,
            'set' => $data->quantity,
            default => throw new \InvalidArgumentException('Invalid adjustment type'),
        };

        if ($newStock < 0) {
            throw new \Exception('Insufficient stock');
        }

        DB::transaction(function () use ($product, $previousStock, $newStock, $data) {
            // Update product stock
            $this->productRepository->update($product, ['stock' => $newStock]);

            // Log history
            $this->historyRepository->log([
                'company_id' => $data->companyId,
                'product_id' => $product->id,
                'user_id' => $data->userId,
                'action' => 'manual_' . $data->adjustmentType,
                'stock_deduction_type' => $product->stock_deduction_type,
                'invoice_quantity' => null,
                'consumed_stock' => $data->quantity,
                'previous_stock' => $previousStock,
                'current_stock' => $newStock,
                'reference_type' => 'manual',
                'reference_id' => null,
                'remarks' => $data->remarks,
                'created_at' => now(),
            ]);
        });

        return $product->fresh();
    }
}
