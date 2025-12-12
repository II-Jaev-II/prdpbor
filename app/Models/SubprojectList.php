<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubprojectList extends Model
{
    protected $table = 'subproject_lists';
    
    protected $fillable = [
        'subproject_name',
    ];
}
