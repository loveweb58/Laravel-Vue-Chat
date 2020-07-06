<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create accounts.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('accounts.view');
    }


    /**
     * Determine whether the user can view the journal.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Account $account
     *
     * @return mixed
     */
    public function view(User $user, Account $account)
    {
        return $user->can('accounts.view');
    }


    /**
     * Determine whether the user can create accounts.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('accounts.create');
    }


    /**
     * Determine whether the user can update the journal.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Account $account
     *
     * @return mixed
     */
    public function update(User $user, Account $account)
    {
        return $user->can('accounts.update');
    }


    /**
     * Determine whether the user can delete the journal.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Account $account
     *
     * @return mixed
     */
    public function delete(User $user, Account $account)
    {
        return $user->can('accounts.delete');
    }
}
