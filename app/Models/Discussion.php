<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Discussion extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['type', 'name', 'creator', 'course_id', 'organization_id'];

    public function discussionmessage()
    {
        return $this->hasMany(DiscussionMessage::class)->with('user')->with('facilitator');
    }
}
