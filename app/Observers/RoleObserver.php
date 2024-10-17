<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{
    /**
     * Handle the Setting "deleted" event.
     */
    public function saved(Role $role): void
    {

    }
}
