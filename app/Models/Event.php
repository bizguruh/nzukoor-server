<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['type', 'venue', 'title', 'description', 'schedule', 'facilitators', 'resource', 'url', 'cover', 'organization_id', 'start', 'end', 'status', 'facilitator_id', 'admin_id'];

    public  function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public  function admin()
    {
        return $this->belongsTo(Organization::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
