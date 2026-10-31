<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();

            // ── Identitas Kerja ──────────────────────
            $table->string('employee_id', 20)->unique()->nullable();
            // Contoh: STF001, STF002
            $table->string('position', 100);
            // Contoh: Marketing Manager, Finance Staff
            $table->string('department', 100);
            // Contoh: Marketing, Finance, Operations, IT
            $table->date('join_date');
            $table->date('end_date')->nullable(); // null = masih aktif

            // ── Level Akses ──────────────────────────
            $table->enum('access_level', [
                'viewer',     // hanya bisa lihat
                'operator',   // bisa input data
                'supervisor', // bisa approve
                'manager',    // bisa semua kecuali system config
            ])->default('operator');

            // ── Supervisor ───────────────────────────
            $table->unsignedBigInteger('supervisor_id')->nullable();
            // staff_id dari atasan langsung

            // ── Status Kerja ─────────────────────────
            $table->enum('employment_status', [
                'full_time',
                'part_time',
                'contract',
                'intern',
            ])->default('full_time');

            $table->enum('work_status', [
                'active',
                'on_leave',
                'resigned',
                'terminated',
            ])->default('active');

            // ── Catatan ──────────────────────────────
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('staff')->onDelete('set null');

            $table->index('department');
            $table->index('access_level');
            $table->index('work_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
};
