<?php
// app/Modules/AgentSystem/Models/AgentBankAccount.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgentBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'bank_name',
        'account_number',
        'account_holder',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function getDisplayAccountAttribute()
    {
        return $this->bank_name . ' - ' .
            Str::mask($this->account_number, '*', 0, -4) .
            ' (' . $this->account_holder . ')';
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
