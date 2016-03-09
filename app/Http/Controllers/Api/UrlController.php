<?php

namespace App\Http\Controllers\Api;

use App\Url;
use App\Transformers\UrlTransformer;

class UrlController extends ApiController
{
    /**
     * Get a user's search's urls.
     *
     * @param int $userId
     * @param int $searchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($userId, $searchId)
    {
        $urls = Url::where([
            'user_id' => $userId,
            'search_id' => $searchId
        ])->paginate(20);

        if ($urls) {
            return $this->respondWithCollection($urls, new UrlTransformer(), 'urls');
        }

        return $this->errorNotFound();
    }

    /**
     * Get a user's search's url.
     *
     * @param int $userId
     * @param int $searchId
     * @param int $urlId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($userId, $searchId, $urlId)
    {
        $url = Url::where([
            'id' => $urlId,
            'user_id' => $userId,
            'search_id' => $searchId
        ])->paginate(20);

        if ($url) {
            return $this->respondWithCollection($url, new UrlTransformer(), 'urls');
        }

        return $this->errorNotFound();
    }

    /**
     * Get all the user urls.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUserUrls($userId)
    {
        $urls = Url::where(['user_id' => $userId])->paginate(20);

        return $this->respondWithCollection($urls, new UrlTransformer(), 'urls');
    }
}
