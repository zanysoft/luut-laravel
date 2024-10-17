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
		Schema::create('cities', function (Blueprint $table) {
			$table->id();
			$table->foreignId('state_id');
			$table->string('country_code', 2)->nullable();
			$table->text('name');
			$table->float('longitude')->nullable()->comment('longitude in decimal degrees (wgs84)');
			$table->float('latitude')->nullable()->comment('latitude in decimal degrees (wgs84)');
			$table->string('time_zone', 100)->nullable();
			$table->boolean('status')->nullable()->default('1');
			$table->timestamps();

			$table->index(['country_code']);
			$table->index(['state_id']);
			$table->index(['status']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::dropIfExists('cities');
	}
};
