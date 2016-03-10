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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the urls for the search.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    /**
     * Get the resources for the search.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * @return mixed
     */
    public function urlsCount()
    {
        return $this->hasOne(Url::class)
            ->selectRaw('search_id, count(*) as aggregate')
            ->groupBy('search_id');
    }

    /**
     * @return mixed
     */
    public function crawledUrlsCount()
    {
        return $this->hasOne(Url::class)
            ->selectRaw('search_id, count(*) as aggregate')
            ->where(['crawled' => true])
            ->groupBy('search_id');
    }

    /**
     * @return mixed
     */
    public function resourcesCount()
    {
        return $this->hasOne(Resource::class)
            ->selectRaw('search_id, count(*) as aggregate')
            ->groupBy('search_id');
    }

    /**
     * Count the search total number of urls.
     *
     * @return int
     */
    public function getUrlsCountAttribute()
    {
        if (!$this->relationLoaded('urlsCount')) {
            $this->load('urlsCount');
        }

        $related = $this->getRelation('urlsCount');

        return ($related) ? (int) $related->aggregate : 0;
    }

    /**
     * Count the search number of crawled urls.
     *
     * @return int
     */
    public function getCrawledUrlsCountAttribute()
    {
        if (!$this->relationLoaded('crawledUrlsCount')) {
            $this->load('crawledUrlsCount');
        }

        $related = $this->getRelation('crawledUrlsCount');

        return ($related) ? (int) $related->aggregate : 0;
    }

    /**
     * Count the search number of resources.
     *
     * @return int
     */
    public function getResourcesCountAttribute()
    {
        if (!$this->relationLoaded('resourcesCount')) {
            $this->load('resourcesCount');
        }

        $related = $this->getRelation('resourcesCount');

        return ($related) ? (int) $related->aggregate : 0;
    }
}
