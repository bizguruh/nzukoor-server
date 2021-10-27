<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Discussion extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['type', 'name', 'description', 'category', 'tags', 'creator', 'course_id', 'tribe_id', 'organization_id', 'user_id', 'facilitator_id', 'admin_id',];


    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function tribe()
    {
        return $this->belongsTo(Tribe::class)->with('discussions', 'users');
    }
    public function contributions()
    {
        return $this->hasMany(Contributor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function discussionmessage()
    {
        return $this->hasMany(DiscussionMessage::class)->with('user',  'discussionmessagecomment')->orderBy('id', 'DESC');
    }
    public function discussionvote()
    {
        return $this->hasMany(DiscussionVote::class);
    }
    public function discussionview()
    {
        return $this->hasOne(DiscussionView::class);
    }
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    protected $hidden = [
        'admin_id',
        'facilitator_id',
        'course_id'
    ];

    protected $casts = [

        'category' => 'array',
        'tags' => 'array'
    ];
}
