<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;
    protected $fillable = ['question_template_id', 'facilitator_id', 'course_id', 'status', 'start', 'end', 'duration', 'feedback', 'type', 'tools'];
    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }

    public function questiontemplate()
    {
        return $this->belongsTo(QuestionTemplate::class, 'question_template_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function library()
    {
        return $this->belongsTo(Library::class);
    }
    public function assessmentresponse()
    {
        return $this->belongsTo(AssessmentResponse::class);
    }
    public function learnerassessment()
    {
        return $this->belongsTo(LearnerAssessment::class);
    }
}
