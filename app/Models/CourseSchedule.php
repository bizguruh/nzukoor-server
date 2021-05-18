<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'day',
        'start_time',
        'end_time',
        'facilitator_id',
        'course_id',
        'organization_id'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
}
