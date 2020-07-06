<?php

namespace App\Policies;

use App\Models\AutoReply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AutoReplyPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create auto_reply.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('auto_reply.view');
    }


    /**
     * Determine whether the user can view the journal.
     *
     * @param  \App\Models\User      $user
     * @param  \App\Models\AutoReply $reply
     *
     * @return mixed
     */
    public function view(User $user, AutoReply $reply)
    {
        return $user->account_id === $reply->account_id && $user->can('auto_reply.view');
    }


    /**
     * Determine whether the user can create auto_reply.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('auto_reply.create');
    }


    /**
     * Determine whether the user can update the journal.
     *
     * @param  \App\Models\User      $user
     * @param  \App\Models\AutoReply $reply
     *
     * @return mixed
     */
    public function update(User $user, AutoReply $reply)
    {
        return $user->account_id === $reply->account_id && $user->can('auto_reply.update');
    }


    /**
     * Determine whether the user can delete the journal.
     *
     * @param  \App\Models\User      $user
     * @param  \App\Models\AutoReply $reply
     *
     * @return mixed
     */
    public function delete(User $user, AutoReply $reply)
    {
        return $user->account_id === $reply->account_id && $user->can('auto_reply.delete');
    }
}
