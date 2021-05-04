<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'title', 'description', 'schedule', 'facilitators', 'resource', 'url', 'cover', 'organization_id'];

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
