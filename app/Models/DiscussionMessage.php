<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DiscussionMessage extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'discussion_messages';
    protected $fillable = ['facilitator_id', 'user_id', 'admin_id', 'message', 'attachment', 'publicId', 'discussion_id', 'organization_id','mediaType'];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
    public function discussionmessagecomment()
    {
        return $this->hasMany(DiscussionMessageComment::class)->with('user', 'discussionmessage')->latest();
    }
    public function discussionmessagevote()
    {
        return $this->hasMany(DiscussionMessageVote::class)->with('user', 'discussionmessage')->latest();
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
}
