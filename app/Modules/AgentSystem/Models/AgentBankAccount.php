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

    public function setPrimary()
    {
        // Reset all other accounts for this agent
        self::where('agent_id', $this->agent_id)
            ->update(['is_primary' => false]);

        // Set this one as primary
        $this->update(['is_primary' => true]);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
