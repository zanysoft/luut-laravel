<?php

namespace App\Models;

use App\Observers\RoleObserver;
use App\Traits\UtilsTrait;
use Spatie\Permission\Models\Role as OriginalRole;

class Role extends OriginalRole
{
    use UtilsTrait;

    /**
     * System default roles
     * @var string[]
     */
    protected static $defaultRoles = [
        'super-admin',
        'admin',
        'manager',
        'sales',
    ];

    protected static function boot()
    {
        parent::boot();

        Role::observe(RoleObserver::class);
    }

    /**
     * @param $permission
     * @return Role|void
     */
    public function addPermission($permission)
    {
        if (!$this->hasPermissionTo($permission)) {
            return $this->givePermissionTo($permission);
        }
    }

    /**
     * @return bool
     */
    public static function isDefaultRole($role): bool
    {
        if ($role instanceof Role) {
            $role = $role->name;
        }

        $roles = self::$defaultRoles;

        return in_array($role, $roles) ? true : false;
    }
}
