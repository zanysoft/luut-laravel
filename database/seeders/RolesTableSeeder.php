<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $items = [[
            'id' => 1,
            'title' => 'Supper Admin',
            'name' => 'super-admin',
            'guard_name' => 'web',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ], [
            'id' => 2,
            'title' => 'Admin',
            'name' => 'admin',
            'guard_name' => 'web',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ], [
            'id' => 3,
            'title' => 'Manager',
            'name' => 'manager',
            'guard_name' => 'web',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ]];

        DB::table(config('permission.table_names.roles'))->truncate();

        DB::table(config('permission.table_names.roles'))->insert($items);

        Permission::resetDefault();


        $tableRrolePermissions = config('permission.table_names.role_has_permissions');

        \DB::table($tableRrolePermissions)->truncate();

        /**
         * Get Available Permissions.
         */
        $permissions = \App\Models\Permission::all();

        /**
         * Attach Permissions to Roles.
         */
        $roleSuperAdmin = \App\Models\Role::where('name', '=', 'super-admin')->first();

        if ($roleSuperAdmin) {
            $roleSuperAdmin->syncPermissions($permissions);
        }

        $roleAdmin = \App\Models\Role::where('name', '=', 'admin')->first();
        if ($roleAdmin) {
            $roleAdmin->syncPermissions(array_merge(Permission::getStaffPermissions(), Permission::getAdminPermissions()));
        }

        $roleManager = \App\Models\Role::where('name', '=', 'manager')->first();
        if ($roleManager) {
            $roleManager->syncPermissions(array_merge(Permission::getStaffPermissions(), Permission::getAdminPermissions()));
        }

        Schema::enableForeignKeyConstraints();
    }
}
