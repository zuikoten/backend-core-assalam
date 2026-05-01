<?php
// database/migrations/2024_01_01_000003_create_agents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->enum('agent_level', ['cabang', 'premium']);
            $table->string('agent_code', 20)->unique();
            $table->string('full_name', 255);
            $table->string('nik', 16)->unique();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('job')->nullable();
            $table->string('company_name')->nullable();

            // Upline system
            $table->unsignedBigInteger('upline_id')->nullable();

            // Registration fee
            $table->enum('registration_fee_status', ['pending', 'paid', 'verified'])->default('pending');
            $table->decimal('registration_fee_amount', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('upline_id')->references('id')->on('agents')->onDelete('set null');

            $table->index(['nik']);
            $table->index(['agent_level']);
            $table->index(['upline_id']);
            $table->index(['registration_fee_status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agents');
    }
};
