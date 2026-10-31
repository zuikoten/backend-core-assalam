<?php

namespace App\Modules\Trip\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'trips';

    protected $fillable = [
        'title',
        'slug',
        'trip_code',
        'trip_category_id',
        'trip_category_service_id',
        'duration_days',
        'duration_nights',
        'trip_schedule',
        'departure_date',
        'return_date',
        'quota_total',
        'team_leader_id',
        'open_from',
        'open_until',
        'overview_title',
        'description',
        'highlights_title',
        'highlights',
        'include_title',
        'includes',
        'exclude_title',
        'excludes',
        'map_title',
        'map_image',
        'map_iframe',
        'gallery',
        'video_urls',
        'faq_title',
        'faq',
        'status',
        'is_published',
        'thumbnail',
        'created_by',
    ];

    protected $casts = [
        'highlights' => 'array',
        'includes' => 'array',
        'excludes' => 'array',
        'gallery' => 'array',
        'video_urls' => 'array',
        'faq' => 'array',
        'departure_date' => 'date',
        'return_date' => 'date',
        'open_from' => 'date',
        'open_until' => 'date',
        'is_published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(TripCategory::class, 'trip_category_id');
    }

    public function categoryService()
    {
        return $this->belongsTo(TripCategoryService::class, 'trip_category_service_id');
    }

    public function teamLeader()
    {
        return $this->belongsTo(Staff::class, 'team_leader_id');
    }

    public function itineraries()
    {
        return $this->hasMany(TripItinerary::class, 'trip_id')->orderBy('sort_order');
    }

    public function packages()
    {
        return $this->hasMany(TripPackage::class, 'trip_id');
    }

    public function batches()
    {
        return $this->hasMany(TripBatch::class, 'trip_id');
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'trip_destination', 'trip_id', 'destination_id');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'trip_activity', 'trip_id', 'activity_id');
    }

    public function difficulties()
    {
        return $this->belongsToMany(Difficulty::class, 'trip_difficulty', 'trip_id', 'difficulty_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('trip_category_id', $categoryId);
    }

    public function scopeBySchedule($query, string $type)
    {
        return $query->where('trip_schedule', $type);
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['open', 'full']);
    }
}
