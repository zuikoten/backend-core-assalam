<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel pilgrim_profiles menggantikan tabel Jamaah.
     * Satu user = satu Jamaah (one-to-one).
     */
    public function up(): void
    {
        Schema::create('pilgrim_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relasi ke user — WAJIB, tidak nullable
            $table->unsignedBigInteger('user_id')->unique();

            // Agen dan dari cabang mana yang menginput (nullable = booking mandiri)
            $table->unsignedBigInteger('agent_id')->nullable();

            // ── Data Keberangkatan ──────────────────
            $table->string('nik', 16)->unique()->nullable();
            $table->string('passport_number', 20)->unique()->nullable();
            $table->date('passport_expiry')->nullable();

            // ── Dokumen Foto ─────────────────────────
            $table->string('foto_ktp')->nullable();
            $table->string('foto_passport')->nullable();

            // ── Status Kelengkapan Profil ────────────
            // incomplete: baru daftar, belum isi dokumen
            // complete: sudah isi semua dokumen
            // verified: sudah diverifikasi admin, boleh berangkat
            $table->enum('profile_status', [
                'incomplete',
                'complete',
                'verified'
            ])->default('incomplete');

            // ── Siapa yang input ─────────────────────
            //Kalau jamaah daftar sendiri → input_by = user_id jamaah itu sendiri
            //Kalau agen yang input → input_by = user_id si agen
            //Kalau admin yang input → input_by = user_id si admin
            $table->unsignedBigInteger('input_by')->nullable();

            // ── Catatan ───────────────────────────────
            $table->text('notes')->nullable();

            // ── Timestamps ───────────────────────────
            $table->timestamps();
            $table->softDeletes();

            // ── Foreign Keys ──────────────────────────
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('agent_id')
                ->references('id')
                ->on('agents')
                ->onDelete('set null');

            /*$table->foreign('branch_id')
                ->references('id')
                ->on('agents')
                ->onDelete('set null');*/

            $table->foreign('input_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // ── Indexes ──────────────────────────────
            $table->index('agent_id');
            $table->index('profile_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilgrim_profiles');
    }
};
