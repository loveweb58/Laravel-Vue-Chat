<?php

namespace App\Policies;

use App\Models\MessageTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessageTemplatePolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create Message Template.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('message_templates.view');
    }


    /**
     * Determine whether the user can create Message Template.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('message_templates.create');
    }


    /**
     * Determine whether the user can update the Message Template.
     *
     * @param  \App\Models\User            $user
     * @param  \App\Models\MessageTemplate $template
     *
     * @return mixed
     */
    public function update(User $user, MessageTemplate $template)
    {
        return $user->account_id === $template->account_id && $user->can('message_templates.update');
    }


    /**
     * Determine whether the user can delete the Message Template.
     *
     * @param  \App\Models\User            $user
     * @param  \App\Models\MessageTemplate $template
     *
     * @return mixed
     */
    public function delete(User $user, MessageTemplate $template)
    {
        return $user->account_id === $template->account_id && $user->can('message_templates.delete');
    }
}
