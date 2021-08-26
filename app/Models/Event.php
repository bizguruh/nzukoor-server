<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['type', 'venue', 'title', 'description', 'schedule', 'facilitators', 'tribe_id', 'resource', 'url', 'cover', 'organization_id', 'start', 'end', 'status', 'facilitator_id', 'admin_id'];

    public function tribe()
    {
        return $this->belongsTo(Tribe::class);
    }

    public  function eventattendance()
    {
        return $this->hasMany(EventAttendance::class);
    }
    public  function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public  function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public  function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
