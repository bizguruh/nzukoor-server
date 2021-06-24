<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionResponse extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'question_template_id', 'response', 'your_score', 'total_score'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function questiontemplate()
    {
        return $this->belongsTo(QuestionTemplate::class);
    }
}
