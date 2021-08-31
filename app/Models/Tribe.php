<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tribe extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'description', 'cover', 'category', 'tags'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class)->with('eventattendance', 'facilitator')->latest();
    }
    public function courses()
    {
        return $this->hasMany(Course::class)->with('courseoutline', 'courseschedule', 'modules', 'questionnaire', 'review', 'enroll', 'viewcount')->latest();
    }
    public function discussions()
    {
        return $this->hasMany(Discussion::class)->with('admin', 'user', 'facilitator', 'discussionmessage', 'discussionvote', 'discussionview')->latest();
    }
    public function feeds()
    {
        return $this->hasMany(Feed::class)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest();
    }
}
