<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllForCompany(int $companyId): Collection
    {
        return Product::where('company_id', $companyId)->orderBy('item_no')->get();
    }

    public function findByIdForCompany(int $id, int $companyId): ?Product
    {
        return Product::where('id', $id)->where('company_id', $companyId)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function findByItemNo(string $itemNo, int $companyId): ?Product
    {
        return Product::where('item_no', $itemNo)->where('company_id', $companyId)->first();
    }

    public function findByName(string $name, int $companyId): ?Product
    {
        return Product::where('name', $name)->where('company_id', $companyId)->first();
    }
}
