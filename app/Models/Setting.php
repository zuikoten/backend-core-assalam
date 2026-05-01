<?php
// app/Models/Setting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description'
    ];

    protected $casts = [
        'setting_value' => 'array',
    ];

    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('setting_key', $key)->first();
            return $setting ? $setting->setting_value : $default;
        });
    }

    public static function set($key, $value)
    {
        Cache::forget("setting_{$key}");
        self::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }
}
