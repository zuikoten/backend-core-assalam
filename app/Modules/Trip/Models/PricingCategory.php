<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class PricingCategory extends Model
{
    protected $table = 'pricing_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
