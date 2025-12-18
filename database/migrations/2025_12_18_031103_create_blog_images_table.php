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
        Schema::create('blog_images', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ourblog_id')->constrained()->cascadeOnDelete();
        $table->string('image');
        $table->string('title')->nullable();
        $table->text('subtitle')->nullable();
        $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_images');
    }
};
