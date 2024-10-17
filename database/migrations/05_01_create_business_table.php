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
        Schema::create('business', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('type_id')->nullable();
            $table->string('name', 255);
            $table->string('slug', 255)->nullable();
            $table->text('description')->nullable();


            $table->string('phone', 20)->nullable();
            $table->string('phone2', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('website', 255)->nullable();

            $table->string('address', 255)->nullable();
            $table->string('address2', 255)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('state', 30)->nullable();
            $table->string('country_code', 3)->default('UK')->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->string('time_zone', 50)->nullable();
            $table->string('language', 50)->nullable();
            $table->text('social_links')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
