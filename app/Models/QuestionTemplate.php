<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'sections', 'interest', 'status', 'totalscore', 'type', 'organization_id', 'admin_id', 'user_id', 'facilitator_id'];
    public function admin()
    {
        return  $this->belongsTo(Admin::class);
    }
    public function assessment()
    {
        return  $this->hasMany(Assessment::class);
    }

    public function facilitator()
    {
        return  $this->belongsTo(Facilitator::class);
    }
    public function module()
    {
        return  $this->belongsToMany(Module::class);
    }

    public function questionresponse()
    {
        return $this->hasMany(QuestionResponse::class);
    }
}
