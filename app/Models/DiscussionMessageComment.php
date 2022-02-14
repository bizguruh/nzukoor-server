<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DiscussionMessageComment extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'discussion_message_comments';
    protected $fillable = ['facilitator_id', 'user_id', 'admin_id', 'message', 'discussion_id', 'discussion_message_id', 'organization_id', 'mediaType', 'attachment'];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
    public function discussionmessage()
    {
        return $this->belongsTo(DiscussionMessage::class);
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
