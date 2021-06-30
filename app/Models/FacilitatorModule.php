<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilitatorModule extends Model
{
    use HasFactory;
    protected $fillable = ['modules', 'facilitator_id', 'course_id'];

    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
