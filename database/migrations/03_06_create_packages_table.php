<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180)->nullable();
            $table->string('slug', 180)->unique()->nullable();

            $table->text('description')->nullable();
            $table->text('payment_note')->nullable();
            $table->mediumText('content')->nullable();

            $table->enum('package_type', ['payment', 'subscription'])->nullable();
            $table->enum('package_layout', ['inline', 'checkout'])->default('inline');

            $table->decimal('amount')->nullable();
            $table->decimal('setup_fee')->nullable();
            $table->integer('trial_period')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('billing_cycle')->nullable();
            $table->integer('tax_rate_type')->nullable();
            $table->integer('tax_rate')->nullable();
            $table->boolean('recurring')->default(0)->nullable();

            $table->string('payment_method', 100)->nullable();
            $table->string('payment_product', 100)->nullable();
            $table->string('payment_plan', 100)->nullable();
            $table->string('payment_interval', 50)->nullable();
            $table->integer('payment_interval_count')->nullable();
            $table->integer('payment_duration')->nullable();
            $table->string('payment_currency', 3)->nullable();


            $table->string('stripe_plan', 100)->nullable();
            $table->string('picture', 250)->nullable();

            $table->boolean('status')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
