<?php
// app/Models/Company.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'gstin',
        'logo_path',
        'signature_path',
        'bank_details',
        'gst_mode_default',
        'gst_rates',
        'gst_settings',
        'invoice_preferences',
        'invoice_prefix',
        'quote_prefix',
        'default_payment_terms',
        'state_code',
        'state_name',
        'address_line_1',
        'address_line_2',
        'city',
        'pincode',
        'phone',
        'website',
        'pan',
        'cin',
        'gstin',
        'pan',
        'gst_mode_default',
        'gst_settings',
        'default_payment_terms',
        'invoice_prefix',
        'quote_prefix',
        'show_hsn_sac',
        'is_active',
    ];

    protected $casts = [
        'bank_details' => 'array',
        'gst_rates' => 'array',
        'gst_settings' => 'array',
        'invoice_preferences' => 'array',
        'gst_mode_default' => 'string',
        'is_active' => 'boolean',
    ];

    public function getGstModeAttribute(): string
    {
        // Check the dedicated column (if exists)
        if (!empty($this->attributes['gst_mode_default'])) {
            return $this->attributes['gst_mode_default'];
        }

        // Fallback to the JSON field
        $settings = $this->gst_settings ?? [];
        return $settings['default_mode'] ?? 'exclusive';
    }

    /**
     * Accessor for the default GST rate (0,5,12,18,28).
     */
    public function getDefaultGstRateAttribute(): int
    {
        $settings = $this->gst_settings ?? [];
        return (int) ($settings['default_rate'] ?? 18);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->hasOne(User::class)->whereHas('roles', function ($q) {
            $q->where('name', 'owner');
        });
    }

    public function getDefaultGstRatesAttribute(): array
    {
        return $this->gst_rates ?? config('gst_rates.default_rates');
    }

    public function getFormattedGstinAttribute(): string
    {
        return formatGSTIN($this->gstin);
    }
}
