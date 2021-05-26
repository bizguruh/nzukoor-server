<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;
    protected $fillable  = ['title', 'module', 'content', 'module_id', 'course_id', 'organization_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
