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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamp('starting_date')->nullable();
            $table->timestamp('ending_date')->nullable();
            $table->enum('status', ["UPCOMMING", "ACTIVE", "COMPLETED", "ARCHIVED"])->default('UPCOMMING');
            $table->boolean("isActive")->default(true);
            $table->string('venue_name')->nullable();
            $table->string('address_line')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('admin_id')->references('id')->on('AuthAdmin')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
