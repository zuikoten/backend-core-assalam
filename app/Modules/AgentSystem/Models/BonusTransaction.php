<?php
// app/Modules/AgentSystem/Models/BonusTransaction.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTransaction extends Model
{
    use HasFactory;

    protected $table = 'agent_bonus_transactions';

    protected $fillable = [
        'agent_id',
        'pilgrim_id',
        'from_agent_id',
        'bonus_type',
        'amount',
        'description',
        'status',
        'payout_id'
    ];

    protected $casts = [
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

    public function payout()
    {
        return $this->belongsTo(AgentBonusPayout::class);
    }

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->bonus_type] ?? $this->bonus_type;
    }

    public function getTotalAmountAttribute()
    {
        return $this->amount;
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
