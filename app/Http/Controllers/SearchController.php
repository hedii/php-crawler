<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * @var \Illuminate\Support\Facades\Auth
     */
    protected $user;

    /**
     * SearchController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->user = Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->view('searches.index', [
            //'searches' => $this->user->searches,
            'searches' => $this->user->searches
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $search = Search::find($id);

        return response()->view('searches.show', [
            'search' => $search,
            'stats' => $this->getSearchStats($search)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('searches.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entrypoint' => 'required|url|max:1000',
            'type' => 'required|in:email,phone,url'
        ]);

        if ($validator->fails()) {
            return back();
        }

        $search = new Search();
        $search->entrypoint = $request->entrypoint;
        $search->type = $request->type;
        $this->user->searches()->save($search);

        exec('cd ' . base_path() . ' && php artisan crawler:crawl ' . $search->id . ' > /dev/null &');

        return redirect('searches/' . $search->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'finished' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return back();
        }

        $search = Search::find($id);
        $search->finished = $request->finished;
        $search->save();

        return back();
    }

    /**
     * Remove the specified user's search.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Search::destroy($id);

        return redirect('searches');
    }

    /**
     * Remove all the user's searches.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroyAll()
    {
        $this->user->searches()->delete();

        return redirect('searches');
    }

    /**
     * Get search's statistics.
     *
     * @param \App\Search $search
     * @return array
     */
    private function getSearchStats(Search $search)
    {
        $total = $search->urls()->count();
        $crawled = $search->urls()->where(['crawled' => true])->count();

        if ($total > 0) {
            return [
                'percentCrawled' => round(($crawled  / $total) * 100),
                'percentNotCrawled' => round(100 - (($crawled  / $total) * 100))
            ];
        }

        return [
            'percentCrawled' => 0,
            'percentNotCrawled' => 0
        ];
    }
}
