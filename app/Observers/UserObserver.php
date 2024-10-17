<?php


namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Listen to the Entry updating event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
        /*if ($user->type != 'admin') {
            $user->notify(new WellcomeNotification());
        }*/
    }

    /**
     * Listen to the Entry updating event.
     *
     * @param User $user
     * @return void
     */
    public function updating(User $user)
    {

    }

    /**
     * Listen to the Entry deleting event.
     *
     * @param User $user
     * @return void
     */
    public function deleting(User $user)
    {
        //$user->tokens()->delete();
    }
}
