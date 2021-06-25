<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'bio',
        'profile',
        'organization_id',
        'email_verified_at',
        'verification',
        'referral_code',
        'interests',
        'age',
        'gender',
        'lga',
        'state',
        'country'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function learnerassessment()
    {
        return $this->hasMany(LearnerAssessment::class);
    }

    public function assessmentresponse()
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function questionresponse()
    {
        return $this->hasOne(QuestionResponse::class);
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


    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function communitylink()
    {
        return $this->hasMany(CourseCommunityLink::class);
    }
    public function coursecommunity()
    {
        return $this->hasMany(CourseCommunity::class);
    }


    public function answeredquestionnaire()
    {
        return $this->hasMany(AnsweredQuestionnaire::class);
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function loginhistory()
    {
        return $this->hasMany(LoginHistory::class);
    }
    public function inbox()
    {
        return $this->hasMany(Inbox::class);
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

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
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

    public function library()
    {
        return $this->hasMany(Library::class);
    }

    public function referral()
    {
        return $this->hasMany(Referral::class);
    }
    public function linkedSocialAccounts()
    {
        return $this->hasMany(LinkedSocialAccount::class);
    }
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
}
