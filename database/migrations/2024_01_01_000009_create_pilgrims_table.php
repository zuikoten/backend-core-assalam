<?php
// database/migrations/2024_01_01_000009_create_pilgrims_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pilgrims', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('full_name', 255);
            $table->string('passport_number', 50)->nullable();
            $table->date('departure_date')->nullable();
            $table->enum('status', ['registered', 'paid', 'departed', 'completed'])->default('registered');

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('agents')->onDelete('set null');

            $table->index(['agent_id']);
            $table->index(['branch_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pilgrims');
    }
};
