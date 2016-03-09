<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * ResourceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Give the user a file containing all the search resources.
     *
     * @param int $searchId
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($searchId)
    {
        $resources = Search::find($searchId)
            ->resources()
            ->get()
            ->lists('name')
            ->unique()
            ->toArray();

        return $this->respondWithFile($resources);
    }

    /**
     * Give the user a file containing all his resources.
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAll()
    {
        $resources = Auth::user()->resources()
            ->get()
            ->lists('name')
            ->unique()
            ->toArray();

        return $this->respondWithFile($resources);
    }

    /**
     * Download the file to the user's browser or flash an error message.
     *
     * @param array $resources
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function respondWithFile(array $resources)
    {
        $filename = uniqid() . '.txt';

        if ($this->createFile($filename, $resources)) {
            return response()->download(storage_path('app/' . $filename));
        }

        flash()->error('There was a problem creating the file. Please try again.');

        return back();
    }

    /**
     * Create a file containing one resource per line. If a file
     * with the same name already exists, return false.
     *
     * @param string $filename
     * @param array $resources
     * @return bool
     */
    private function createFile($filename, array $resources)
    {
        if ( ! Storage::disk('local')->exists($filename)) {
            Storage::disk('local')
                ->put($filename, implode("\r\n", $resources));

            return true;
        }

        return false;
    }
}
