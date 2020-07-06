<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{

    use HandlesAuthorization;


    /**
     * Determine whether the user can create appointments.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->can('appointments.view');
    }


    /**
     * Determine whether the user can view the journal.
     *
     * @param  \App\Models\User        $user
     * @param  \App\Models\Appointment $appointment
     *
     * @return mixed
     */
    public function view(User $user, Appointment $appointment)
    {
        return $user->account_id === $appointment->account_id && $user->can('appointments.view');
    }


    /**
     * Determine whether the user can create appointments.
     *
     * @param  \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('appointments.create');
    }


    /**
     * Determine whether the user can update the journal.
     *
     * @param  \App\Models\User        $user
     * @param  \App\Models\Appointment $appointment
     *
     * @return mixed
     */
    public function update(User $user, Appointment $appointment)
    {
        return $user->account_id === $appointment->account_id && $user->can('appointments.update');
    }


    /**
     * Determine whether the user can delete the journal.
     *
     * @param  \App\Models\User        $user
     * @param  \App\Models\Appointment $appointment
     *
     * @return mixed
     */
    public function delete(User $user, Appointment $appointment)
    {
        return $user->account_id === $appointment->account_id && $user->can('appointments.delete');
    }
}
