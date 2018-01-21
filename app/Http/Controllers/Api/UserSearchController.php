<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserSearchRequest;
use App\Http\Resources\SearchCollection;
use App\Http\Resources\SearchResource;
use App\Jobs\CrawlJob;
use App\User;

class UserSearchController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\User $user
     * @return \App\Http\Resources\SearchCollection
     */
    public function index(User $user): SearchCollection
    {
        return new SearchCollection($user->searches()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\UserSearchRequest $request
     * @param \App\User $user
     * @return \App\Http\Resources\SearchResource
     */
    public function store(UserSearchRequest $request, User $user): SearchResource
    {
        $search = $user->searches()->create($request->validated());

        CrawlJob::dispatch($search);

        return new SearchResource($search->fresh());
    }
}
