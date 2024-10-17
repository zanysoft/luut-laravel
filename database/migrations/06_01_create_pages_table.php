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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['standard', 'terms', 'privacy', 'tips', 'contact']);
            $table->text('name')->nullable();
			$table->string('slug', 150)->nullable();
            $table->text('title')->nullable();
            $table->string('picture', 255)->nullable();
            $table->mediumtext('content')->nullable();
			$table->text('seo_title')->nullable();
			$table->text('seo_description')->nullable();
			$table->text('seo_keywords')->nullable();
            $table->boolean('excluded_from_footer')->nullable()->default('0');
            $table->boolean('status')->nullable()->default('1');
            $table->timestamps();
            $table->softDeletes();

			$table->index(['slug']);
            $table->index(['status']);
			$table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
