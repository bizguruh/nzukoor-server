<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculums';

    protected $fillable = ['organization_id', 'course_id', 'content'];

    public function course()
    {
        return $this->belongsToMany(Course::class);
    }
}
