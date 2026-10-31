<?php
// app/Models/PasswordHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    protected $fillable = [
        'user_id',
        'is_temporary',
        'must_change',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'is_temporary' => 'boolean',
        'must_change' => 'boolean',
    ];

    //Relasi ke User (Many-to-One inverse)
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
