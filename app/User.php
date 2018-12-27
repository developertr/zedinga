<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function counters() {
        return $this->hasOne('App\UserCounter');
    }

    public function privacies() {
        return $this->hasOne('App\UserPrivacy');
    }

    public function locations() {
        return $this->hasMany('App\UserLocations');
    }

    public function posts() {
        return $this->hasMany('App\Posts');
    }
}
