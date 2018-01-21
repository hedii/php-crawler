<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SearchEmailController extends Controller
{
    /**
     * Send a file with all the search's emails.
     *
     * @param \App\Search $search
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Search $search): BinaryFileResponse
    {
        $emails = $search->emails->pluck('name')->unique()->all();

        $tempFileName = str_random() . '.txt';

        Storage::put($tempFileName, implode("\r\n", $emails));

        return response()
            ->download(storage_path("app/{$tempFileName}"))
            ->deleteFileAfterSend(true);
    }
}
