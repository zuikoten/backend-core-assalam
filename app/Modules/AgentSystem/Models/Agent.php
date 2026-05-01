<?php
// app/Modules/AgentSystem/Models/Agent.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_level',
        'agent_code',
        'full_name',
        'nik',
        'birth_date',
        'gender',
        'job',
        'company_name',
        'upline_id',
        'registration_fee_status',
        'registration_fee_amount'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'registration_fee_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agent) {
            $agent->agent_code = 'AGT' . strtoupper(uniqid());
        });
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function upline()
    {
        return $this->belongsTo(Agent::class, 'upline_id');
    }

    public function downlines()
    {
        return $this->hasMany(Agent::class, 'upline_id');
    }

    public function addresses()
    {
        return $this->hasMany(AgentAddress::class);
    }

    public function documents()
    {
        return $this->hasMany(AgentDocument::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(AgentBankAccount::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }
}
