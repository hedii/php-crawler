<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Url extends Model
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
        'search_id' => 'integer',
        'is_crawled' => 'boolean'
    ];

    /**
     * Get the search that owns the url.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function search(): BelongsTo
    {
        return $this->belongsTo(Search::class);
    }

    /**
     * Whether the url is not crawled yet.
     *
     * @return bool
     */
    public function getIsNotCrawledAttribute(): bool
    {
        return ! $this->is_crawled;
    }

    /**
     * Scope a query to only include not crawled urls.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotCrawled(Builder $query): Builder
    {
        return $query->where('is_crawled', false);
    }
}
