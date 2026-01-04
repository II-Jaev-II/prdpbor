<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GeotagPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_order_id',
        'photo_path',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrolledActivity(): BelongsTo
    {
        return $this->belongsTo(EnrollActivity::class, 'travel_order_id', 'to_num');
    }

    public function getPhotoUrlAttribute(): string
    {
        return Storage::url($this->photo_path);
    }
}
