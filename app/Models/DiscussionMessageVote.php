<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionMessageVote extends Model
{
    use HasFactory;

    protected $table = 'discussion_message_votes';
    protected $fillable = ['user_id', 'vote',  'discussion_message_id'];

    public function discussionmessage()
    {
        return $this->belongsTo(DiscussionMessage::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
