<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollCount extends Model
{
    use HasFactory;
    protected $fillable = ['count', 'course_id', 'facilitator_id', 'organization_id'];

    public function course()
    {
        return $this->belongsTo(Course::class)->with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll');
    }
}
