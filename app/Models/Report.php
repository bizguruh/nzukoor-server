<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'type_report_id', 'message', 'user_id', 'status'];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
    public function discussionmessage()
    {
        return $this->belongsTo(DiscussionMessage::class);
    }

    public function feedcomment()
    {
        return $this->belongsTo(FeedComment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
