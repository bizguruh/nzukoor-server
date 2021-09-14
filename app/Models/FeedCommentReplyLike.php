<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedCommentReplyLike extends Model
{
    use HasFactory;
    protected $table = 'comment_reply_likes';
    protected $fillable = ['feed_comment_reply_id', 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function feedcommentreply()
    {
        return $this->belongsTo(FeedCommentReply::class);
    }
}
