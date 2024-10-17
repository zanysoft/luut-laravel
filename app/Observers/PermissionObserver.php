<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use App\Models\Permission;

class PermissionObserver
{
    /**
     * Handle the City "created" event.
     */
    public function created(Permission $permission): void
    {
        Cache::flush();
    }

    /**
     * Handle the City "updated" event.
     */
    public function updated(Permission $permission): void
    {
        Cache::flush();
    }

    /**
     * Handle the City "deleted" event.
     */
    public function deleted(Permission $permission): void
    {
        //
    }

    /**
     * Handle the City "restored" event.
     */
    public function restored(Permission $permission): void
    {
        //
    }

    /**
     * Handle the City "force deleted" event.
     */
    public function forceDeleted(Permission $permission): void
    {
        //
    }
}
