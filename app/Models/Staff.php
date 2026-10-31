<?php
// app/Models/Staff.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'position',
        'department',
        'join_date',
        'end_date',
        'access_level',
        'supervisor_id',
        'employment_status',
        'work_status',
        'notes',
    ];

    protected $casts = [
        'join_date' => 'date',
        'end_date' => 'date',
    ];

    protected $hidden = [
        'user_id',
    ];

    //Relasi ke User (One-to-One inverse)
     
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relasi ke supervisor (atasan langsung)
     
    public function supervisor()
    {
        return $this->belongsTo(Staff::class, 'supervisor_id');
    }

    //Relasi ke bawahannya
     
    public function subordinates()
    {
        return $this->hasMany(Staff::class, 'supervisor_id');
    }
}
