<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAllForCompany(int $companyId): Collection;
    public function findByIdForCompany(int $id, int $companyId): ?Product;
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): void;
    public function findByItemNo(string $itemNo, int $companyId): ?Product;
    public function findByName(string $name, int $companyId): ?Product;
}
