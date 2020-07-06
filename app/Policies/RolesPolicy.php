<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolesPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('roles.view');
    }


    /**
     * Determine whether the user can view the journal.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Role $role
     *
     * @return mixed
     */
    public function view(User $user, Role $role)
    {
        return $user->account_id === $role->account_id && $user->can('roles.view');
    }


    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('roles.create');
    }


    /**
     * Determine whether the user can update the journal.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Role $role
     *
     * @return mixed
     */
    public function update(User $user, Role $role)
    {
        return $user->account_id === $role->account_id && $user->can('roles.update');
    }


    /**
     * Determine whether the user can delete the journal.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Role $role
     *
     * @return mixed
     */
    public function delete(User $user, Role $role)
    {
        return $user->account_id === $role->account_id && $user->can('roles.delete');
    }
}
