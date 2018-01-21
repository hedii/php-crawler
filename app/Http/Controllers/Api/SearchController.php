<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\SearchResource;
use App\Search;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SearchController extends ApiController
{
    /**
     * Display the specified resource.
     *
     * @param \App\Search $search
     * @return \App\Http\Resources\SearchResource
     */
    public function show(Search $search): SearchResource
    {
        return new SearchResource($search);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\SearchRequest $request
     * @param \App\Search $search
     * @return \App\Http\Resources\SearchResource
     */
    public function update(SearchRequest $request, Search $search): SearchResource
    {
        $search->update(array_filter($request->validated()));

        return new SearchResource($search->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Search $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Search $search): JsonResponse
    {
        $search->delete();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
