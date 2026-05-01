<?php
// app/Modules/AgentSystem/Models/AgentAddress.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'province_id',
        'regency_id',
        'district_id',
        'village',
        'full_address',
        'postal_code',
        'is_domicile'
    ];

    protected $casts = [
        'is_domicile' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function province()
    {
        return $this->belongsTo(\App\Models\Region::class, 'province_id');
    }

    public function regency()
    {
        return $this->belongsTo(\App\Models\Region::class, 'regency_id');
    }

    public function district()
    {
        return $this->belongsTo(\App\Models\Region::class, 'district_id');
    }

    public function getCompleteAddressAttribute()
    {
        return collect([
            $this->full_address,
            $this->village,
            $this->district?->name,
            $this->regency?->name,
            $this->province?->name,
            $this->postal_code ? "({$this->postal_code})" : null
        ])->filter()->implode(', ');
    }
}
