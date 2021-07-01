<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighestEarningCourse extends Model
{
    use HasFactory;
    protected $fillable = ['revenue', 'course_id',  'organization_id'];


    public function course()
    {
        return $this->belongsTo(Course::class)->with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount');
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
