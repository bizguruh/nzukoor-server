<?php

namespace App\Models;

use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tribe extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'description', 'cover',  'tags', 'type', 'amount'];



    public function requests()
    {
        return $this->hasMany(TribeRequest::class);
    }
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
        return $this->hasMany(Event::class)->with('eventattendance')->latest();
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
        return $this->hasMany(Feed::class)->with( 'user', 'comments', 'likes', 'stars')->latest();
    }

    public function getTribeOwnerAttribute()
    {

        $user = $this->users()->with('accountdetail')->where('is_owner', 1)->first();
        if (!$user) {
            return null;
        }
        return [
            'name' => $user->name,
            'split_code' => $user->accountdetail ? $user->accountdetail->group_split_code : null,
            'data' => new UserResource($user)
        ];
    }
    public function getTribeOwner()
    {

        $user = $this->users()->where('is_owner', 1)->first();
        return $user ? $user : null;
    }
    public function getMembership($user_id)
    {


        $member = $this->users()->where('user_id', $user_id)->first();
        return $member ? true : false;
    }

    public function getUsersCount()
    {

        $user = $this->users()->count();

        return $user;
    }
    protected $casts = [

        'tags' => 'array',

    ];
}
