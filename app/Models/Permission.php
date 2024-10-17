<?php

namespace App\Models;

use App\Traits\UtilsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as OriginalPermission;

class Permission extends OriginalPermission
{
    use UtilsTrait;

    protected static function boot()
    {
        parent::boot();

        //Permission::observe(PermissionObserver::class);

        static::addGlobalScope('orderbyname', function (Builder $builder) {
            $builder->orderByRaw(" SUBSTRING_INDEX(name,'.',-1) ");//->orderByRaw(" SUBSTRING_INDEX(name,'.',-1) ");
        });
    }

    public function scopeWithModel(Builder $builder)
    {
        $builder->addSelect(DB::raw(" SUBSTRING_INDEX(name,'.',-1) as module "))
            ->addSelect(DB::raw(" SUBSTRING_INDEX(name,'.',1) as task "));
    }

    /**
     * Default Super Admin users permissions
     *
     * @return array
     */
    public static function getSuperAdminPermissions()
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'permissions.view',
            'permissions.reset',
            'permissions.edit',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete'
        ];

        return $permissions;
    }

    /**
     * Default Super Admin users permissions
     *
     * @return array
     */
    public static function getAdminPermissions()
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'permissions.view',
            'roles.view'
        ];

        return $permissions;
    }

    /**
     * Default Staff users permissions
     *
     * @return array
     */
    public static function getStaffPermissions()
    {
        $permissions = [
            'dashboard.access',
        ];

        return $permissions;
    }

    /**
     * Get all Admin Controllers public methods
     *
     * @return array
     */
    public static function defaultPermissions()
    {
        return [
            "dashboard.access",

            "users.view",
            "users.create",
            "users.edit",
            "users.delete",

            "permissions.view",
            "permissions.reset",
            "permissions.edit",

            "roles.view",
            "roles.create",
            "roles.edit",
            "roles.delete",

            //"make.ajax",
            //"view.ajax",

            "countries.view",
            "countries.create",
            "countries.edit",
            "countries.delete",

            "categories.view",
            "categories.create",
            "categories.edit",
            "categories.delete",

            "packages.view",
            "packages.create",
            "packages.edit",
            "packages.delete",

            "business-types.view",
            "business-types.create",
            "business-types.edit",
            "business-types.delete",

            "settings.edit",
            "settings.view",
        ];
    }

    /**
     * @return string[]
     */
    public static function resetDefault()
    {

        $db_permissions = Permission::all()->pluck('name')->toArray();
        $default_permissions = Permission::defaultPermissions();

        $inserted = array_diff($default_permissions, $db_permissions);
        $removed = array_diff($db_permissions, $default_permissions);

        $roles = Role::all()->keyBy('id');
        $_role_permissions = [];
        foreach ($roles as $rl) {
            $_role_permissions[$rl->id] = $rl->permissions()->pluck('name')->toArray();
        }

        if (!empty($inserted) || !empty($removed)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table(config('permission.table_names.role_has_permissions'))->truncate();
            DB::table(config('permission.table_names.permissions'))->truncate();

            foreach ($default_permissions as $permission) {
                DB::table(config('permission.table_names.permissions'))->insert([
                    'name' => $permission,
                    'guard_name' => 'web',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $db_permissions = Permission::all()->pluck('name', 'id')->toArray();

            foreach ($roles as $role) {
                if ($role->name == 'super-admin' || $role->id == 1) {
                    $role->permissions()->sync(array_keys($db_permissions));
                } else {
                    foreach ($db_permissions as $dp) {
                        if (isset($_role_permissions[$role->id]) && !empty($_role_permissions[$role->id])) {
                            if (in_array($dp, $_role_permissions[$role->id])) {
                                $role->givePermissionTo($dp);
                            }
                        }
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return ['message' => 'Your permissions updated successfully', 'status' => 'success'];
        } else {
            return ['message' => 'Your permissions already updated', 'status' => 'warning'];
        }
    }
}
