<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Search extends Model
{
    public const STATUS_CREATED = 'CREATED';
    public const STATUS_RUNNING = 'RUNNING';
    public const STATUS_FINISHED = 'FINISHED';
    public const STATUS_PAUSED = 'PAUSED';
    public const STATUS_FAILED = 'FAILED';

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
        'user_id' => 'integer',
        'is_limited' => 'boolean'
    ];

    /**
     * Get the user that owns the search.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the urls for the search.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function urls(): HasMany
    {
        return $this->hasMany(Url::class);
    }

    /**
     * Get the emails for the search.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Whether the search is in "created" state.
     *
     * @return bool
     */
    public function getIsCreatedAttribute(): bool
    {
        return $this->status === self::STATUS_CREATED;
    }

    /**
     * Whether the search is in "running" state.
     *
     * @return bool
     */
    public function getIsRunningAttribute(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * Whether the search is in "finished" state.
     *
     * @return bool
     */
    public function getIsFinishedAttribute(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Whether the search is in "failed" state.
     *
     * @return bool
     */
    public function getIsFailedAttribute(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get the search's base domain.
     *
     * @return string
     */
    public function getDomainAttribute(): string
    {
        $parsed = parse_url($this->url);

        return "{$parsed['scheme']}://{$parsed['host']}";
    }
}
