<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();

            // ── Identitas ────────────────────────────
            $table->string('full_name');
            $table->string('avatar')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('nationality', 100)->nullable()->default('Indonesia');

            // ── Kontak & Alamat ──────────────────────
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();

            // ── Bio ──────────────────────────────────
            $table->text('bio')->nullable();
            $table->string('website')->nullable();

            // ── Social Media ─────────────────────────
            $table->json('social_media')->nullable();
            // Format: {"instagram": "username", "tiktok": "username", "custom_platform": "url"}

            // ── Preferences ──────────────────────────
            $table->string('language', 10)->default('id');
            $table->string('timezone', 50)->default('Asia/Jakarta');
            $table->boolean('notification_email')->default(true);
            $table->boolean('notification_whatsapp')->default(true);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};
