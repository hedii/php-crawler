<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserEmailController extends Controller
{
    /**
     * Send a file with all the users's emails.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request, User $user): BinaryFileResponse
    {
        $emails = $user->emails->pluck('name')->unique()->all();

        $tempFileName = str_random() . '.txt';

        Storage::put($tempFileName, implode("\r\n", $emails));

        return response()
            ->download(storage_path("app/{$tempFileName}"))
            ->deleteFileAfterSend(true);
    }
}
