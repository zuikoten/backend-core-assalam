<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Modifikasi:
     * 1. Tambah kolom user_id (nullable)
     * 2. Tambah kolom used_at
     * 3. Ubah kolom type dari string ke enum
     */
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();

            // Tambah user_id (nullable karena OTP bisa diminta sebelum login)
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('whatsapp', 20);
            $table->string('code', 100);

            // Ubah type dari string ke enum
            $table->enum('type', [
                'reset_password',
                'claim_account',
                'phone_verify',
                'login'
            ])->default('reset_password');

            $table->boolean('is_used')->default(false);

            // Tambah used_at untuk audit trail
            $table->timestamp('used_at')->nullable();

            $table->timestamp('expires_at');
            $table->timestamps();

            // Foreign key untuk user_id
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Index untuk performa query
            $table->index(['whatsapp', 'type']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
