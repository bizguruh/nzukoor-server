<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table = 'feedbacks';
    protected $fillable = ['user_id', 'course_id', 'event_id', 'feedback', 'rating'];

    public function event()
    {
        $this->belongsTo(Event::class);
    }
    public function course()
    {
        $this->belongsTo(Course::class);
    }
    public function user()
    {
        $this->belongsTo(User::class);
    }
}
