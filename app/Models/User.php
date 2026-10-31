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
        'email',
        'password',
        'phone',
        'whatsapp',
        'status',
        'email_verified',
        'email_verified_at',
        'phone_verified',
        'phone_verified_at',
        'last_login_at',
        'last_login_ip',
        'is_temporary_password',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'last_login_at' => 'datetime',
        'is_temporary_password' => 'boolean',
    ];

    /**
     * Relasi ke Agent (One-to-One)
     * Agent terhubung ke User via user_id di agents table
     */
    public function agent()
    {
        return $this->hasOne(\App\Modules\AgentSystem\Models\Agent::class);
    }

    //Relasi ke roles (Many-to-Many via user_roles pivot table)
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'user_roles')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    //Relasi ke user_profile (One-to-One)
    public function profile()
    {
        return $this->hasOne(\App\Models\UserProfile::class);
    }

    //Relasi ke staff (One-to-One untuk staff internal)
    public function staff()
    {
        return $this->hasOne(\App\Models\Staff::class);
    }

    //Relasi ke pilgrim_profile (One-to-One untuk data keberangkatan)
    public function pilgrimProfile()
    {
        return $this->hasOne(\App\Models\PilgrimProfile::class);
    }

    //Relasi ke password_histories
    public function passwordHistories()
    {
        return $this->hasMany(\App\Models\PasswordHistory::class);
    }

    //Cek apakah user bisa register agent baru
    public function canRegisterAgent()
    {
        return $this->agent?->status === 'active';
    }

    ///Cek apakah user memiliki role tertentu
    //Penggunaan: $user->hasRole('admin') atau $user->hasRole(['admin', 'super_admin'])

    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            return $this->roles()->whereIn('name', $roles)->exists();
        }

        return $this->roles()->where('name', $roles)->exists();
    }

    //Cek apakah user adalah admin (admin atau super_admin)
    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    //Cek apakah user adalah super admin
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    //Cek apakah user adalah staff internal
    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    //Cek apakah user adalah agent
    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    //Get all role names user memiliki
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    //Assign role ke user
    public function assignRole($roleName, ?int $assignedBy = null): self
    {
        $role = \App\Models\Role::where('name', $roleName)->firstOrFail();

        $this->roles()->attach($role->id, [
            'assigned_at' => now(),
            'assigned_by' => $assignedBy,
        ]);

        return $this;
    }

    //Hapus role dari user
    public function removeRole($roleName): self
    {
        $role = \App\Models\Role::where('name', $roleName)->first();

        if ($role) {
            $this->roles()->detach($role->id);
        }

        return $this;
    }

    //Get agent atau create baru jika belum ada
    public function getAgentOrCreate()
    {
        return $this->agent ?? $this->agent()->create([]);
    }
}
