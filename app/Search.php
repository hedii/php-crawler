<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'searches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'entrypoint', 'user_id', 'domain_limit', 'finished'];

    /**
     * Get the user that owns the search.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the urls for the search.
     */
    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    /**
     * Get the resources for the search.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
