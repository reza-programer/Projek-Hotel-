<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_jp')->nullable();
            $table->string('type');
            $table->decimal('price', 15, 2);
            $table->integer('capacity');
            $table->integer('size');
            $table->string('floor');
            $table->text('description');
            $table->text('long_description');
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->json('features')->nullable();
            $table->string('color_theme')->default('sakura');
            $table->boolean('is_available')->default(true);
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
