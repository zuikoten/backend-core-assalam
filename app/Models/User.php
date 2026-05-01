<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'agent_id',
        'email',
        'password',
        'phone',
        'whatsapp',
        'role',
        'status',
        'email_verified',
        'phone_verified'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
    ];

    public function agent()
    {
        return $this->hasOne(\App\Modules\AgentSystem\Models\Agent::class);
    }

    public function canRegisterAgent()
    {
        return $this->agent?->status === 'active';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getAgentOrCreate()
    {
        return $this->agent ?? $this->agent()->create([]);
    }
}
