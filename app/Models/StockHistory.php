<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    public $timestamps = false; // created_at handled by migration

    protected $fillable = [
        'company_id',
        'product_id',
        'user_id',
        'action',
        'stock_deduction_type',
        'invoice_quantity',
        'consumed_stock',
        'previous_stock',
        'current_stock',
        'reference_type',
        'reference_id',
        'remarks',
        'created_at',
    ];

    protected $casts = [
        'invoice_quantity' => 'decimal:2',
        'consumed_stock' => 'decimal:2',
        'previous_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
    