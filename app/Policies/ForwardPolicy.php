<?php

namespace App\Policies;

use App\Models\Forward;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForwardPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create forwarding.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('forwarding.view');
    }


    /**
     * Determine whether the user can view the journal.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Forward $forward
     *
     * @return mixed
     */
    public function view(User $user, Forward $forward)
    {
        return $user->account_id === $forward->account_id && $user->can('forwarding.view');
    }


    /**
     * Determine whether the user can create forwarding.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('forwarding.create');
    }


    /**
     * Determine whether the user can update the journal.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Forward $forward
     *
     * @return mixed
     */
    public function update(User $user, Forward $forward)
    {
        return $user->account_id === $forward->account_id && $user->can('forwarding.update');
    }


    /**
     * Determine whether the user can delete the journal.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Forward $forward
     *
     * @return mixed
     */
    public function delete(User $user, Forward $forward)
    {
        return $user->account_id === $forward->account_id && $user->can('forwarding.delete');
    }
}
