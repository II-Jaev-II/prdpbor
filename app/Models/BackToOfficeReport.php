<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackToOfficeReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_num',
        'start_date',
        'end_date',
        'purpose',
        'place',
        'accomplishment',
        'photos',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'photos' => 'array',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
