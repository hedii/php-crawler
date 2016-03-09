<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Transformers\UserTransformer;

class UserController extends ApiController
{
    /**
     * Get a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($userId)
    {
        $user = User::find($userId);

        if ($user) {
            return $this->respondWithItem($user, new UserTransformer(), 'users');
        }

        return $this->errorNotFound();
    }
}
