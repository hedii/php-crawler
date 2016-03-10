<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'urls';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'crawled', 'user_id', 'search_id'];

    /**
     * Get the user that owns the url.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the url.
     */
    public function search()
    {
        return $this->belongsTo(Search::class);
    }
}
