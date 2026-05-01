<?php
// app/Modules/AgentSystem/Models/BonusTransaction.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'pilgrim_count',
        'bonus_type',
        'amount',
        'description',
        'status',
        'payment_id'
    ];

    protected $casts = [
        'pilgrim_count' => 'integer',
        'amount' => 'decimal:2',
    ];

    const TYPES = [
        'fixed_fee' => 'Fee Tetap',
        'coaching_fee' => 'Fee Pembinaan',
        'region_bonus' => 'Bonus Wilayah'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->bonus_type] ?? $this->bonus_type;
    }

    public function getTotalAmountAttribute()
    {
        return $this->amount * $this->pilgrim_count;
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('bonus_type', $type);
    }
}
