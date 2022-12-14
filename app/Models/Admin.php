<?php

namespace App\Models;

use Egulias\EmailValidator\Warning\Comment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function revenue()
    {
        return $this->hasOne(Revenue::class);
    }


    public function questiondrafts()
    {
        return $this->hasMany(QuestionDraft::class);
    }
    public function questiontemplates()
    {
        return $this->hasMany(QuestionTemplate::class);
    }
    public function discussionrequest()
    {
        return $this->hasMany(DiscussionRequest::class);
    }

    public function privatediscusion()
    {
        return $this->hasMany(PrivateDiscussionMember::class);
    }

    public function contribution()
    {
        return $this->hasMany(Contributor::class);
    }

    public function communitylink()
    {
        return $this->hasMany(CourseCommunityLink::class);
    }
    public function coursecommunity()
    {
        return $this->hasMany(CourseCommunity::class);
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function inbox()
    {
        return $this->hasMany(Inbox::class);
    }
    public function loginhistory()
    {
        return $this->hasMany(LoginHistory::class);
    }
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function event()
    {
        return $this->hasMany(Event::class);
    }
    public function course()
    {
        return $this->hasMany(Course::class);
    }

    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }
    public function comments()
    {
        return $this->hasMany(FeedComment::class);
    }
    public function stars()
    {
        return $this->hasMany(FeedStar::class);
    }

    public function likes()
    {
        return $this->hasMany(FeedLike::class);
    }
    public function curriculum()
    {
        return $this->hasMany(Curriculum::class);
    }
    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function discussionmessage()
    {
        return $this->hasMany(DiscussionMessage::class);
    }
    public function discussionvote()
    {
        return $this->hasMany(DiscussionVote::class);
    }
    public function connections()
    {
        return $this->hasMany(Connection::class);
    }

    public function module()
    {
        return $this->hasMany(Module::class);
    }
    public function referral()
    {
        return $this->hasMany(Referral::class);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'email_verified_at',
        'profile',
        'verification',
        'organization_id',
        'referral_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
