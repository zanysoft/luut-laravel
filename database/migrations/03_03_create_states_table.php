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
		Schema::create('states', function (Blueprint $table) {
			$table->id();
			$table->string('code', 100);
			$table->string('country_code', 2)->nullable();
			$table->text('name');
			$table->boolean('status')->nullable()->default('1');

			$table->unique(['code']);
			$table->index(['country_code']);
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
		Schema::dropIfExists('states');
	}
};
