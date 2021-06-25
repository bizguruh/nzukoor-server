<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerAssessment extends Model
{
    use HasFactory;
    protected $fillable = ['status', 'user_id', 'assessment_id'];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }
}
