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
		Schema::create('categories', function (Blueprint $table) {
			$table->id();
			$table->integer('parent_id')->unsigned()->nullable();
			$table->text('name');
			$table->string('slug', 150)->nullable();
			$table->text('description')->nullable();
			$table->boolean('hide_description')->nullable();
			$table->string('picture', 100)->nullable();
			$table->string('icon_class', 100)->nullable();
			$table->text('seo_title')->nullable();
			$table->text('seo_description')->nullable();
			$table->text('seo_keywords')->nullable();
			$table->boolean('status')->nullable()->default('1');

			$table->index(['slug']);
			$table->index(['parent_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::dropIfExists('categories');
	}
};
