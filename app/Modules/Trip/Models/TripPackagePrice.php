<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class TripPackagePrice extends Model
{
    protected $table = 'trip_package_prices';

    protected $fillable = [
        'trip_package_id',
        'pricing_category_id',
        'regular_price',
        'is_sale',
        'sale_price',
        'is_active',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_sale' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(TripPackage::class, 'trip_package_id');
    }

    public function pricingCategory()
    {
        return $this->belongsTo(PricingCategory::class, 'pricing_category_id');
    }

    public function getActivePriceAttribute(): float
    {
        return ($this->is_sale && $this->sale_price !== null)
            ? (float) $this->sale_price
            : (float) $this->regular_price;
    }
}
