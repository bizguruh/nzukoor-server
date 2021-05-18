<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOutline extends Model
{
    use HasFactory;

    protected $fillable = [
        'overview',
        'knowledge_areas',
        'modules',
        'duration',
        'certification',
        'faqs',
        'course_id',
        'additional_info',
        'organization_id'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
