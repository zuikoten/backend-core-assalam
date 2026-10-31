<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ── Kredensial Login ─────────────────────
            $table->string('email')->unique();
            $table->string('phone', 20)->unique()->nullable();
            $table->string('whatsapp', 20)->unique()->nullable();
            $table->string('password');

            // ── Status Akun ──────────────────────────
            $table->enum('status', [
                'pending',
                'active',
                'inactive',
                'suspended',
                'rejected',
            ])->default('pending');

            // ── Verifikasi ───────────────────────────
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('phone_verified_at')->nullable();

            // ── Keamanan ─────────────────────────────
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            $table->boolean('is_temporary_password')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // ── Index ────────────────────────────────
            $table->index('email');
            $table->index('phone');
            $table->index('whatsapp');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};