<?php

namespace App\Policies;

use App\Models\Blacklist;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlacklistPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create Blacklist.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('blacklist.view');
    }


    /**
     * Determine whether the user can create Blacklist.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('blacklist.create');
    }


    /**
     * Determine whether the user can delete the Blacklist.
     *
     * @param  \App\Models\User      $user
     * @param  \App\Models\Blacklist $blacklist
     *
     * @return mixed
     */
    public function delete(User $user, Blacklist $blacklist)
    {
        return $user->account_id === $blacklist->account_id && $user->can('blacklist.delete');
    }
}
