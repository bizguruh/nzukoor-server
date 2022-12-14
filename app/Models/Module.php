<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['module', 'modules', 'organization_id', 'course_id', 'facilitator_id', 'admin_id', 'question_template_id'];

    public  function questiontemplate()
    {
        return $this->hasOne(QuestionTemplate::class);
    }

    public  function course()
    {
        return $this->belongsTo(Course::class);
    }
    public  function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }

    public  function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public  function courseoutline()
    {
        return $this->belongsTo(CourseOutline::class);
    }
    public function questionnaire()
    {
        return $this->hasMany(Questionnaire::class);
    }
}
