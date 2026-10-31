<?php
// app/Models/UserProfile.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'avatar',
        'birth_date',
        'gender',
        'nationality',
        'address',
        'city',
        'province',
        'postal_code',
        'bio',
        'website',
        'social_media',
        'language',
        'timezone',
        'notification_email',
        'notification_whatsapp',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'social_media' => 'array',
        'notification_email' => 'boolean',
        'notification_whatsapp' => 'boolean',
    ];

    protected $hidden = [
        'user_id',
    ];

    //Relasi ke User (One-to-One inverse)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
