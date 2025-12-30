<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_registrations', function (Blueprint $table) {
            $table->id();

            // DATA PIC & PERUSAHAAN
            $table->string('last_name');
            $table->string('company');
            $table->string('primary_phone');
            $table->string('mobile_phone');
            $table->string('primary_email');
            $table->string('website')->nullable();

            // ALAMAT
            $table->text('street');
            $table->string('city');
            $table->string('postal_code');

            // MASTER
            $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dpd_location_id')->nullable()->constrained()->nullOnDelete();

            // DATA USAHA
            $table->string('annual_revenue')->nullable();
            $table->integer('number_of_employees')->nullable();
            $table->string('nib_siup');
            $table->string('npwp_usaha');

            // FILE
            $table->string('ktp_pic')->nullable();

            // STATUS
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->text('admin_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_registrations');
    }
};
