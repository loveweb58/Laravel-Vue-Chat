<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create users.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('users.view');
    }


    /**
     * Determine whether the user can view the journal.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $sub_user
     *
     * @return mixed
     */
    public function view(User $user, User $sub_user)
    {
        return $user->account_id === $sub_user->account_id && $user->can('users.view');
    }


    /**
     * Determine whether the user can create users.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('users.create');
    }


    /**
     * Determine whether the user can update the journal.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $sub_user
     *
     * @return mixed
     */
    public function update(User $user, User $sub_user)
    {
        return $user->account_id === $sub_user->account_id && $user->can('users.update');
    }


    /**
     * Determine whether the user can delete the journal.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $sub_user
     *
     * @return mixed
     */
    public function delete(User $user, User $sub_user)
    {
        return $user->account_id === $sub_user->account_id && $user->can('users.delete');
    }
}
