<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create groups.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('groups.view');
    }


    /**
     * Determine whether the user can view the journal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group $group
     *
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $user->account_id === $group->account_id && $user->can('groups.view');
    }


    /**
     * Determine whether the user can create groups.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('groups.create');
    }


    /**
     * Determine whether the user can update the journal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group $group
     *
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $user->account_id === $group->account_id && $user->can('groups.update');
    }


    /**
     * Determine whether the user can delete the journal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group $group
     *
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $user->account_id === $group->account_id && $user->can('groups.delete');
    }
}
