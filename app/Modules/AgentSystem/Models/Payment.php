<?php
// app/Modules/AgentSystem/Models/Payment.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'payment_type',
        'amount',
        'payment_method',
        'bank_account',
        'transaction_ref',
        'status',
        'proof_document_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    const TYPES = [
        'registration_fee' => 'Biaya Pendaftaran',
        'bonus_fee' => 'Fee Bonus',
        'other' => 'Lainnya'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function document()
    {
        return $this->belongsTo(AgentDocument::class, 'proof_document_id');
    }

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->payment_type] ?? $this->payment_type;
    }

    public function scopeRegistration($query)
    {
        return $query->where('payment_type', 'registration_fee');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }
}
