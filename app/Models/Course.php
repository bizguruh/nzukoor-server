<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'course_code',
        'cover',
        'organization_id',
        'type',
        'amount',
        'progress',
        'current_module',
        'total_modules'

    ];

    public function facilitatormodules()
    {
        return $this->hasMany(FacilitatorModule::class);
    }

    public function assessment()
    {
        return  $this->hasMany(Assessment::class);
    }

    public function viewcount()
    {
        return $this->hasOne(CourseViewCount::class);
    }
    public function enroll()
    {
        return $this->hasOne(EnrollCount::class);
    }
    public function revenue()
    {
        return $this->belongsTo(Revenue::class);
    }

    public function review()
    {
        return $this->hasMany(Review::class)->with('user');
    }

    public  function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public  function admin()
    {
        return $this->belongsTo(Organization::class);
    }

    public  function modules()
    {
        return $this->hasMany(Module::class)->with('questionnaire');
    }
    public function feedback()
    {
        return $this->hasMany(Feedback::class)->with('user');
    }

    public function curriculum()
    {
        return $this->hasOne(Curriculum::class);
    }

    public function courseoutline()
    {
        return $this->hasOne(CourseOutline::class);
    }
    public function courseschedule()
    {
        return $this->hasMany(CourseSchedule::class);
    }
    public function coursefacilitator()
    {
        return $this->hasMany(CourseFacilitator::class);
    }

    public function library()
    {
        return $this->hasMany(Library::class);
    }
    public function questionnaire()
    {
        return $this->hasMany(Questionnaire::class);
    }
}
