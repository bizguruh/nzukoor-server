<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tribe extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'description', 'cover', 'category', 'tags', 'type', 'amount'];

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
        return $this->hasMany(Discussion::class)->with('user', 'discussionmessage', 'discussionvote', 'discussionview')->latest();
    }
    public function feeds()
    {
        return $this->hasMany(Feed::class)->with('admin', 'user', 'facilitator', 'comments', 'likes', 'stars')->latest();
    }

    public function getTribeOwnerAttribute()
    {

        $user = $this->users()->with('accountdetail')->where('is_owner', 1)->first();
        if (!$user->accountdetail) {
            return null;
        }
        return [
            'name' => $user->name,
            'split_code' => $user->accountdetail->group_split_code
        ];
    }
    public function getTribeOwner()
    {

        $user = $this->users()->where('is_owner', 1)->first();
        return $user ? $user->id : null;
    }
    public function getUsersCount()
    {

        $user = $this->users()->count();

        return $user;
    }
    protected $casts = [

        'tags' => 'array',
        'category' => 'array'
    ];
}
