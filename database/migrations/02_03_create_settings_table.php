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
		Schema::create('settings', function (Blueprint $table) {
			$table->id();
			$table->string('key', 100);
			$table->string('name', 255);
			$table->mediumtext('values')->nullable();
			$table->string('description', 500)->nullable();
			$table->integer('order')->default(0)->unsigned()->nullable();
			$table->boolean('status')->nullable();
			$table->timestamps();
            $table->softDeletes();

			$table->unique(['key']);
			$table->index(['order']);
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
		Schema::dropIfExists('settings');
	}
};
