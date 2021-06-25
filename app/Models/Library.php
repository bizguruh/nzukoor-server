<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Library extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['course_id', 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function course()
    {
        return $this->hasMany(Course::class);
    }
    public function assessment()
    {
        return $this->belongsTo(Assessment::class)->with('questiontemplate');
    }
    public function assessmentresponse()
    {
        return $this->belongsTo(AssessmentResponse::class);
    }
}
