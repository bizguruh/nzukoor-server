<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'knowledge_areas',
        'curriculum',
        'modules',
        'duration',
        'certification',
        'faqs',
        'date',
        'time',
        'facilitators',
        'cover',
        'organization_id'
    ];
    public  function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public  function admin()
    {
        return $this->belongsTo(Organization::class);
    }

    public  function module()
    {
        return $this->hasMany(Module::class)->with('facilitator');
    }
    public function feedback()
    {
        return $this->hasMany(Feedback::class)->with('user');
    }

    public function curriculum()
    {
        return $this->hasOne(Curriculum::class);
    }
}
