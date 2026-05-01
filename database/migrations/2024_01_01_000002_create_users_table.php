<?php
// database/migrations/2024_01_01_000002_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agent_id')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->unique();
            $table->string('whatsapp', 20)->unique();
            $table->enum('role', ['admin', 'agent', 'pending'])->default('pending');
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'suspended'])->default('pending');
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);

            $table->timestamps();

            $table->index(['email']);
            $table->index(['phone']);
            $table->index(['whatsapp']);
            $table->index(['status']);
            $table->index(['agent_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
