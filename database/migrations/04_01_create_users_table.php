<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('email',150)->unique();
            $table->string('username',50)->nullable()->unique();
            $table->string('password',255)->nullable();

            $table->text('about')->nullable();
            $table->string('gender',10)->nullable();
            $table->date('dob')->nullable();
            $table->string('phone',20)->nullable();

            $table->string('address',255)->nullable();
            $table->string('address2',255)->nullable();
            $table->string('city',30)->nullable();
            $table->string('state',30)->nullable();
            $table->string('country_code',3)->default('UK')->nullable();
            $table->string('zipcode',10)->nullable();
            $table->string('time_zone',50)->nullable();
            $table->string('language',50)->nullable();

            $table->string('avatar',255)->nullable();

            $table->string('provider',255)->nullable();
            $table->string('provider_id',255)->nullable();

            $table->string('user_type',255)->nullable();

            $table->boolean('status')->default(1);
            $table->boolean('blocked')->default(0);
            $table->boolean('closed')->default(0);
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
