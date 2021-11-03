<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Organization extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'contact_name',
        'contact_phone',
        'contact_address',
        'interest',
        'bio',
        'logo',
        'email_verified_at',
        'verification',
        'referral_code',
        'role_id'
    ];

    public function revenue()
    {
        return $this->hasOne(Revenue::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
    public function referral()
    {
        return $this->hasMany(Referral::class);
    }
    public function facilitator()
    {
        return $this->hasMany(Facilitator::class);
    }
    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
    public function event()
    {
        return $this->hasMany(Event::class);
    }
    public function course()
    {
        return $this->hasMany(Course::class);
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
