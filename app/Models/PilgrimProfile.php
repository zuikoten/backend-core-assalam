<?php
// app/Models/PilgrimProfile.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilgrimProfile extends Model
{
    use HasFactory;

    protected $table = 'pilgrim_profiles';

    protected $fillable = [
        'user_id',
        'agent_id',
        'nik',
        'passport_number',
        'passport_expiry',
        'foto_ktp',
        'foto_passport',
        'profile_status',
        'input_by',
        'notes',
    ];

    protected $casts = [
        'passport_expiry' => 'date',
    ];

    /**
     * Relasi ke User (One-to-One)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Relasi ke Agent (Many-to-One)
     * agent_id nullable = booking mandiri
     */
    public function agent()
    {
        return $this->belongsTo(\App\Modules\AgentSystem\Models\Agent::class);
    }

    /**
     * Cek apakah profil sudah lengkap
     */
    public function isComplete(): bool
    {
        return $this->profile_status === 'complete';
    }

    /**
     * Cek apakah profil sudah diverifikasi
     */
    public function isVerified(): bool
    {
        return $this->profile_status === 'verified';
    }

    /**
     * Scope: hanya profil yang belum lengkap
     */
    public function scopeIncomplete($query)
    {
        return $query->where('profile_status', 'incomplete');
    }

    /**
     * Scope: hanya profil yang sudah lengkap
     */
    public function scopeComplete($query)
    {
        return $query->where('profile_status', 'complete');
    }

    /**
     * Scope: hanya profil yang sudah diverifikasi
     */
    public function scopeVerified($query)
    {
        return $query->where('profile_status', 'verified');
    }
}
