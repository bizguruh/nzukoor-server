<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnsweredQuestionnaire extends Model
{
    use HasFactory;
    protected $table = 'answered_questionnaire';
    protected $fillable = ['user_id', 'question_template_id', 'module_id', 'course_id', 'content', 'status', 'your_score', 'total_score'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    public function questiontemplate()
    {
        return $this->belongsTo(QuestionTemplate::class);
    }
}
