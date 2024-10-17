<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('users')->truncate();

        $superAdminRole = Role::where('name', '=', 'super-admin')->first();
        /*
         * Add Users
         */
        $newUser = User::create([
            'first_name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'status' => 1
        ]);
        $newUser->syncRoles([$superAdminRole]);

        $adminRole = Role::where('name', '=', 'admin')->first();
        $newUser2 = User::create([
            'first_name' => 'Mubashar',
            'email' => 'mubasharahmad.pk@gmail.com',
            'username' => 'mubashar',
            'password' => bcrypt('123456'),
            'status' => 1
        ]);

        $newUser2->syncRoles([$adminRole]);

        Schema::enableForeignKeyConstraints();

        User::create([
            'first_name' => 'User',
            'email' => 'user@user.com',
            'username' => 'user',
            'password' => bcrypt('user'),
            'status' => 1
        ]);
    }
}
