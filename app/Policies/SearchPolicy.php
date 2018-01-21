<?php

namespace App\Policies;

use App\User;
use App\Search;
use Illuminate\Auth\Access\HandlesAuthorization;

class SearchPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the search.
     *
     * @param \App\User $user
     * @param \App\Search $search
     * @return bool
     */
    public function view(User $user, Search $search): bool
    {
        return $user->id === $search->user_id;
    }

    /**
     * Determine whether the user can create searches.
     *
     * @param \App\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the search.
     *
     * @param \App\User $user
     * @param \App\Search $search
     * @return bool
     */
    public function update(User $user, Search $search): bool
    {
        return $user->id === $search->user_id;
    }

    /**
     * Determine whether the user can delete the search.
     *
     * @param \App\User $user
     * @param \App\Search $search
     * @return bool
     */
    public function delete(User $user, Search $search): bool
    {
        return $user->id === $search->user_id;
    }
}
