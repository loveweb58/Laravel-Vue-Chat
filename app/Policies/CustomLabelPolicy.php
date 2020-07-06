<?php

namespace App\Policies;

use App\Models\CustomLabel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomLabelPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create Custom Label.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('custom_labels.view');
    }


    /**
     * Determine whether the user can create Custom Label.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('custom_labels.create');
    }


    /**
     * Determine whether the user can update the Custom Label.
     *
     * @param  \App\Models\User        $user
     * @param  \App\Models\CustomLabel $label
     *
     * @return mixed
     */
    public function update(User $user, CustomLabel $label)
    {
        return $user->account_id === $label->account_id && $user->can('custom_labels.update');
    }


    /**
     * Determine whether the user can delete the Custom Label.
     *
     * @param  \App\Models\User        $user
     * @param  \App\Models\CustomLabel $label
     *
     * @return mixed
     */
    public function delete(User $user, CustomLabel $label)
    {
        return $user->account_id === $label->account_id && $user->can('custom_labels.delete');
    }
}
