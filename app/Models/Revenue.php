<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;
    protected $fillable = ['revenue', 'course_id', 'user_id', 'admin_id', 'facilitator_id', 'organization_id'];


    public function course()
    {
        return $this->belongsTo(Course::class)->with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
