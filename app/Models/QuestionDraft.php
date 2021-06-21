<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionDraft extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'sections', 'interest', 'organization_id', 'admin_id', 'user_id', 'facilitator_id'];
    public function admin()
    {
        return  $this->belongsTo(Admin::class);
    }
    public function facilitator()
    {
        return  $this->belongsTo(Facilitator::class);
    }
}
