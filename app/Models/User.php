<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;

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
        'country',
        'voice',
        'username',
        'role_id',
        'show_age', 'show_name', 'show_email'
    ];

    public function accountdetail()
    {
        return $this->hasOne(AccountDetail::class);
    }



    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function tribes()
    {
        return $this->belongsToMany(Tribe::class)->withPivot('is_owner');
    }

    public function revenue()
    {
        return $this->hasOne(Revenue::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public  function event()
    {
        return $this->hasMany(Event::class);
    }

    public  function eventattendance()
    {
        return $this->hasMany(EventAttendance::class);
    }
    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function memberassessment()
    {
        return $this->hasMany(MemberAssessment::class);
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
    public function feedcommentreplies()
    {
        return $this->hasMany(FeedCommentReply::class);
    }
    public function feedcommentlikes()
    {
        return $this->hasMany(FeedCommentLike::class);
    }
    public function feedcommentreplylikes()
    {
        return $this->hasMany(FeedCommentReplyLike::class);
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
    public function discussionmessagecomment()
    {
        return $this->hasMany(DiscussionMessageComment::class);
    }
    public function discussionmessagevote()
    {
        return $this->hasMany(DiscussionMessageVote::class);
    }
    public function discussionvote()
    {
        return $this->hasMany(DiscussionVote::class);
    }
    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
    public function pendingconnections()
    {
        return $this->hasMany(PendingConnectionMessage::class);
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
        'organization_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'show_age' => 'boolean',
        'show_name' => 'boolean',
        'show_email' => 'boolean',
        'verification' => 'boolean',
        'email_verified_at' => 'datetime',
        'interests' => 'array',

    ];

    public function findForPassport($username)
    {
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $this->where('email', $username)->first();
        } else {
            return $this->where('username', $username)->first();
        }
    }

    public function save(array $options = array())
    {
        if (empty($this->username)) {
            $this->username = mt_rand(0000000, 9999999);
        }
        return parent::save($options);
    }
}
