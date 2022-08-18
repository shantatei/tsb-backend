<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'profile_photo',
        'email',
        'password',
    ];


    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'user_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'user_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
