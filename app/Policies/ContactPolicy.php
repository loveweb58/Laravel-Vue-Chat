<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create contacts.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('contacts.view');
    }


    /**
     * Determine whether the user can view the contact.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function all(User $user)
    {
        return $user->can('contacts.view');
    }


    /**
     * Determine whether the user can create contacts.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('contacts.create');
    }


    /**
     * Determine whether the user can update the contact.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Contact $contact
     *
     * @return mixed
     */
    public function update(User $user, Contact $contact)
    {
        return $user->account_id === $contact->account_id && $user->can('contacts.update');
    }


    /**
     * Determine whether the user can delete the contact.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Contact $contact
     *
     * @return mixed
     */
    public function delete(User $user, Contact $contact)
    {
        return $user->account_id === $contact->account_id && $user->can('contacts.delete');
    }
}
