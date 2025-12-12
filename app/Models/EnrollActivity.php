<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollActivity extends Model
{
    protected $table = 'enrolled_activities';
    
    protected $fillable = [
        'activity_name',
        'unit_component',
        'purpose',
        'purpose_type',
        'subproject_id',
        'subproject_name',
        'start_date',
        'end_date',
    ];
}
