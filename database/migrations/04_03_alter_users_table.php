<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->change();
            $table->string('password', 255)->nullable()->change();

            if (!Schema::hasColumn('users', 'address2')) {
                $table->string('address2', 255)->nullable()->after('address');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
