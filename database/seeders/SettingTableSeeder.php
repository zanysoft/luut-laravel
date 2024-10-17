<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'id' => 1,
                'key' => 'app',
                'name' => 'Application Setting',
                'values' => '{"name":"Luut Social","logo":"images/logo.png","email":"info@luut.social","phone":"","address":""}',
            ], [
                'id' => 2,
                'key' => 'mail',
                'name' => 'Mail Setting',
                'values' => '{"from_email":"no-reply@luut.social","driver":"sendmail","timeout":"300"}',
            ], [
                'id' => 3,
                'key' => 'seo',
                'name' => 'SEO Setting',
                'values' => '{"facebook_link":"","instagram_link":"","twitter_link":"","linkedin_link":""}',
            ]
        ];

        DB::table('settings')->truncate();

        DB::table('settings')->insert($items);
    }
}
