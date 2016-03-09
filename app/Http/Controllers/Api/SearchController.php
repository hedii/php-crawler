<?php

namespace App\Http\Controllers\Api;

Use App\Search;
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
        $searches = Search::where(['user_id' => $userId])->get();

        return $this->respondWithCollection($searches, new SearchTransformer(), 'searches');
    }

    /**
     * Get one user's search. The user id parameter is required
     * because it is part of the api endpoint url.
     *
     * @param int $userId
     * @param int $searchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($userId, $searchId)
    {
        $search = Search::find($searchId);

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
            $search = new Search();
            $search->entrypoint = $request->entrypoint;
            $search->type = $request->type;
            Auth::user()->searches()->save($search);

            exec('cd ' . base_path() . ' && php artisan crawler:crawl ' . $search->id . ' > /dev/null &');

            return $this->respondWithCreated('Search has been created.');
        }

        return $this->errorWrongArgs($validator->errors()->first());
    }

    /**
     * Update a user's search: mark it as finished. The user id
     * parameter is required because it is part of the api
     * endpoint url.
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
            $search = Search::find($searchId);
            $search->finished = $request->input('finished');
            $search->save();

            return $this->respondWithCreated('The search has been stopped.');
        }

        return $this->errorWrongArgs($validator->errors()->first());
    }
}
