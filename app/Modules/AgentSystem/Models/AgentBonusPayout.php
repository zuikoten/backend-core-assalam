<?php

namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentBonusPayout extends Model
{
    use HasFactory;

    protected $table = 'agent_bonus_payouts';

    protected $fillable = [
        'agent_id',
        'bank_account_id',
        'amount',
        'status',
        'transaction_ref',
        'paid_at',
        'notes',
        'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'is_primary' => 'boolean',
    ];

    const STATUSES = [
        'pending' => 'Menunggu',
        'processed' => 'Diproses',
        'failed' => 'Gagal'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(AgentBankAccount::class, 'bank_account_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class, 'payout_id');
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
