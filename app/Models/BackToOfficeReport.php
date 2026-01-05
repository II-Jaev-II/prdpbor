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
        'travel_order_id',
        'enrolled_activity_id',
        'start_date',
        'end_date',
        'purpose',
        'place',
        'accomplishment',
        'photos',
        'monitoring_report',
        'status',
        'approval_id',
        'approved_by',
        'superior_remarks',
        'returned_at',
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

    /**
     * Get the enrolled activity associated with this report.
     */
    public function enrollActivity(): BelongsTo
    {
        return $this->belongsTo(EnrollActivity::class, 'enrolled_activity_id');
    }

    /**
     * Get the user who approved this report.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
