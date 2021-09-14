<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedCommentLike extends Model
{
    use HasFactory;
    protected $table = 'comment_likes';
    protected $fillable = ['feed_comment_id', 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function feedcomment()
    {
        return $this->belongsTo(FeedComment::class);
    }
}
