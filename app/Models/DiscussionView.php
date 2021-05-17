<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionView extends Model
{
    use HasFactory;

    protected $fillable = ['view', 'discussion_id'];

    public function discussion()
    {
        $this->belongsTo(Discussion::class);
    }
}
