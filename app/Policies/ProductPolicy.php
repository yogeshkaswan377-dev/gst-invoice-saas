<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin', 'staff']);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->current_company_id === $product->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->current_company_id === $product->company_id
            && $user->hasAnyRole(['owner', 'admin']);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->current_company_id === $product->company_id
            && $user->hasAnyRole(['owner', 'admin']);
    }

    public function adjustStock(User $user, Product $product): bool
    {
        return $user->current_company_id === $product->company_id
            && $user->hasAnyRole(['owner', 'admin']);
    }
}
