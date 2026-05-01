<?php
// app/Models/Region.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\RegionType;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_type',
        'code',
        'name',
        'parent_id'
    ];

    protected $casts = [
        'region_type' => RegionType::class,
    ];

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function scopeProvince($query)
    {
        return $query->where('region_type', 'province');
    }

    public function scopeRegency($query)
    {
        return $query->where('region_type', 'regency');
    }

    public function scopeDistrict($query)
    {
        return $query->where('region_type', 'district');
    }

    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->full_name . ' - ' . $this->name;
        }
        return $this->name;
    }
}
