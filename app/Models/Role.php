<?php
// app/Models/Role.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    //Relasi ke users (Many-to-Many via user_roles)
     
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    //Scope untuk role yang aktif
     
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //Cek apakah role ini aktif
     
    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
