<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'name',
        'description',
        'hsn_sac_code',
        'quantity',
        'consumed_stock',
        'unit',
        'unit_price',
        'original_unit_price',
        'discount_type',
        'discount_value',
        'discount_amount',
        'gst_rate',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'line_total',
        'line_total_with_gst',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',  
        'consumed_stock' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'original_unit_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'line_total_with_gst' => 'decimal:2',
    ];

    /**
     * Item belongs to invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Item belongs to product (optional)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
