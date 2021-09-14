<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedComment extends Model
{
    use HasFactory;

    protected $fillable = ['feed_id', 'comment', 'admin_id', 'user_id', 'facilitator_id', 'organization_id'];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function feed()
    {
        return $this->belongsTo(Feed::class)->with('admin', 'user', 'facilitator');
    }
    public function feedcommentreplies()
    {
        return $this->hasMany(FeedCommentReply::class)->with('user', 'feedcommentreplylikes')->latest();
    }
    public function feedcommentlikes()
    {
        return $this->hasOne(FeedCommentLike::class);
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
}
