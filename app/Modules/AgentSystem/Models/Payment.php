<?php
// app/Modules/AgentSystem/Models/Payment.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'agent_payments';

    protected $fillable = [
        'agent_id',
        'amount',
        'payment_method',
        'bank_account_id',
        'transaction_ref',
        'status',
        'paid_at',
        'proof_document_id',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(AgentBankAccount::class, 'bank_account_id');
    }

    public function document()
    {
        return $this->belongsTo(AgentDocument::class, 'proof_document_id');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }
}
