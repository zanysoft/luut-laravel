<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(): void
	{
		Schema::create('media', function (Blueprint $table) {
			$table->id();
			$table->morphs('model');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');

            $table->string('name',255);
            $table->string('file_name',255);
            $table->string('mime_type',200)->nullable();
            $table->string('disk',100);

            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();

			$table->boolean('status')->nullable()->default('1');

            $table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::dropIfExists('media');
	}
};
