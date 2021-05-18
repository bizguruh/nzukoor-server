<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFacilitator extends Model
{
    use HasFactory;

    protected $fillable = [
        'facilitator_id',
        'course_id',
        'organization_id'
    ];
}
