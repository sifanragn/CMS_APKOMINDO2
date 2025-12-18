<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('agenda_images', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agenda_id')
              ->constrained()
              ->cascadeOnDelete();
        $table->string('image');
        $table->string('title')->nullable();
        $table->text('caption')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('agenda_images');
}

};
