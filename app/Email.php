<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'search_id' => 'integer'
    ];

    /**
     * Get the search that owns the email.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function search(): BelongsTo
    {
        return $this->belongsTo(Search::class);
    }
}
