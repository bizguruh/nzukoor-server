<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedCommentReply extends Model
{
    use HasFactory;
    protected $fillable = ['feed_id', 'feed_comment_id', 'message', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function feedcomment()
    {
        return $this->belongsTo(FeedComment::class);
    }
    public function feedcommentreplylikes()
    {
        return $this->hasMany(FeedCommentReplyLike::class);
    }
}
