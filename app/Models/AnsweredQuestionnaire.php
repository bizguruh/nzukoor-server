<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnsweredQuestionnaire extends Model
{
    use HasFactory;
    protected $table = 'answered_questionnaire';
    protected $fillable = ['user_id', 'questionnaire_id', 'module_id', 'course_id', 'content'];

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
}
