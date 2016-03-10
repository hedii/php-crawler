<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Transformers\SearchTransformer;
use Illuminate\Support\Facades\Validator;

class SearchController extends ApiController
{
    /**
     * Get all the user's searches.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($userId)
    {
        $searches = User::find($userId)
            ->searches()
            ->with(['urlsCount', 'crawledUrlsCount', 'resourcesCount'])
            ->get();

        return $this->respondWithCollection($searches, new SearchTransformer(), 'searches');
    }

    /**
     * Get one user's search.
     *
     * @param int $userId
     * @param int $searchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($userId, $searchId)
    {
        $search = User::find($userId)
            ->searches()
            ->with(['urlsCount', 'crawledUrlsCount', 'resourcesCount'])
            ->find($searchId);

        if ($search) {
            return $this->respondWithItem($search, new SearchTransformer(), 'searches');
        }

        return $this->errorNotFound();
    }

    /**
     * Store a user's search.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entrypoint' => 'required|url|max:1000',
            'type' => 'required|in:email,phone,url'
        ]);

        if (!$validator->fails()) {
            $search = User::find(Auth::user()->id)
                ->searches()
                ->create($request->all());

            exec('cd ' . base_path() . ' && php artisan crawler:crawl ' . $search->id . ' > /dev/null &');

            return $this->respondWithCreated('Search has been created.');
        }

        return $this->errorWrongArgs($validator->errors()->first());
    }

    /**
     * Update a user's search: mark it as finished.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     * @param int $searchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $userId, $searchId)
    {
        $validator = Validator::make($request->all(), [
            'finished' => 'required|boolean'
        ]);

        if (!$validator->fails()) {
            User::find($userId)
                ->searches()
                ->find($searchId)
                ->update(['finished' => $request->input('finished')]);

            return $this->respondWithCreated('The search has been stopped.');
        }

        return $this->errorWrongArgs($validator->errors()->first());
    }
}
