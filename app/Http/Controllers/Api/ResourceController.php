<?php

namespace App\Http\Controllers\Api;

use App\Resource;
use App\Transformers\ResourceTransformer;

class ResourceController extends ApiController
{
    /**
     * Get all the user's searches' resources.
     *
     * @param int $userId
     * @param int $searchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($userId, $searchId)
    {
        $resources = Resource::where([
            'user_id' => $userId,
            'search_id' => $searchId
        ])->get();

        if ($resources) {
            return $this->respondWithCollection($resources, new ResourceTransformer(), 'resources');
        }

        return $this->errorNotFound();
    }

    /**
     * Get a user's search's resource.
     *
     * @param int $userId
     * @param int $searchId
     * @param int $resourceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($userId, $searchId, $resourceId)
    {
        $resource = Resource::where([
            'id' => $resourceId,
            'user_id' => $userId,
            'search_id' => $searchId
        ])->get();

        if ($resource) {
            return $this->respondWithCollection($resource, new ResourceTransformer(), 'resources');
        }

        return $this->errorNotFound();
    }
}
