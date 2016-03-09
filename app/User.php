<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the searches for user.
     */
    public function searches()
    {
        return $this->hasMany(Search::class);
    }

    /**
     * Get the urls for user.
     */
    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    /**
     * Get the resources for user.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
