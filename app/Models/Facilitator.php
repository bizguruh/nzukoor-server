<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Facilitator extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'bio',
        'profile',
        'qualifications',
        'organization_id',
        'email_verified_at',
        'verification',
        'referral_code'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }


    public function module()
    {
        return $this->hasMany(Module::class);
    }

    public function discussionmessage()
    {
        return $this->hasMany(DiscussionMessage::class);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
