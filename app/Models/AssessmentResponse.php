<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentResponse extends Model
{
    use HasFactory;

    public $fillable = ['user_id', 'assessment_id', 'your_score', 'total_score', 'response'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }
    public function library()
    {
        return $this->belongsTo(Library::class);
    }
}
