<?php
// app/Modules/AgentSystem/Models/Pilgrim.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pilgrim extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'branch_id',
        'full_name',
        'passport_number',
        'departure_date',
        'status'
    ];

    protected $casts = [
        'departure_date' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function branch()
    {
        return $this->belongsTo(Agent::class, 'branch_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }
}
