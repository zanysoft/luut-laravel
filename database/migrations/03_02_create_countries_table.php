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
		Schema::create('countries', function (Blueprint $table) {
			$table->id();
			$table->char('code', 2);
			$table->char('iso3', 3)->nullable();
			$table->text('name')->nullable();

			$table->char('continent_code', 4)->nullable();
			$table->string('currency_code', 3)->nullable();
			$table->string('phone', 100)->nullable();
			$table->string('time_zone', 50)->nullable();
			$table->string('date_format', 100)->nullable();
			$table->string('datetime_format', 100)->nullable();
			$table->enum('admin_type', ['0', '1', '2'])->nullable()->default('0');
			$table->boolean('status')->nullable()->default('1');
			$table->timestamps();

			$table->unique(['code']);
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
		Schema::dropIfExists('countries');
	}
};
