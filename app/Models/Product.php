<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'hsn_sac_code',
        'unit_price',
        'gst_rate',
        'unit',
        'is_active',
        'stock',
        'stock_unit',
        'stock_deduction_type',
        'consumption_per_piece',
        'minimum_stock',
        'selling_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'decimal:2',
        'consumption_per_piece' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * Product belongs to a company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Search products by name or HSN
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('hsn_sac_code', 'like', "%{$term}%");
        });
    }
}
