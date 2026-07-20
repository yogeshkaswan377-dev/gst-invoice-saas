<?php

namespace App\Services;

use App\DTOs\ProductData;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getAllForCompany(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Product::forCompany($companyId)->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('hsn_sac_code', 'LIKE', "%{$search}%")
                    ->orWhere('item_no', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['gst_rate'])) {
            $query->where('gst_rate', $filters['gst_rate']);
        }

        return $query->paginate(12);
    }

    public function findByIdForCompany(int $id, int $companyId): ?Product
    {
        return $this->productRepository->findByIdForCompany($id, $companyId);
    }

    public function create(ProductData $data): Product
    {
        return $this->productRepository->create([
            'company_id'          => $data->companyId,
            'item_no'             => $data->itemNo,
            'name'                => $data->name,
            'description'         => $data->description,
            'hsn_sac_code'        => $data->hsnSacCode,
            'unit_price'          => $data->unitPrice,
            'gst_rate'            => $data->gstRate,
            'unit'                => $data->unit,
            'is_active'           => $data->isActive,
            'stock'               => $data->stock,
            'stock_unit'          => $data->stockUnit,
            'stock_deduction_type' => $data->stockDeductionType,
            'consumption_per_piece' => $data->consumptionPerPiece,
            'minimum_stock'       => $data->minimumStock,
            'selling_price'       => $data->sellingPrice,
        ]);
    }

    public function update(Product $product, ProductData $data): Product
    {
        return $this->productRepository->update($product, [
            'item_no'             => $data->itemNo,
            'name'                => $data->name,
            'description'         => $data->description,
            'hsn_sac_code'        => $data->hsnSacCode,
            'unit_price'          => $data->unitPrice,
            'gst_rate'            => $data->gstRate,
            'unit'                => $data->unit,
            'is_active'           => $data->isActive,
            'stock_unit'          => $data->stockUnit,
            'stock_deduction_type' => $data->stockDeductionType,
            'consumption_per_piece' => $data->consumptionPerPiece,
            'minimum_stock'       => $data->minimumStock,
            'selling_price'       => $data->sellingPrice,
        ]);
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}
