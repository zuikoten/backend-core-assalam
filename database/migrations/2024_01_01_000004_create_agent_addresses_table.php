<?php
// database/migrations/2024_01_01_000004_create_agent_addresses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('regency_id');
            $table->unsignedBigInteger('district_id');
            $table->string('village', 255)->nullable();
            $table->text('full_address');
            $table->string('postal_code', 10)->nullable();
            $table->boolean('is_domicile')->default(true);

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('province_id')->references('id')->on('regions');
            $table->foreign('regency_id')->references('id')->on('regions');
            $table->foreign('district_id')->references('id')->on('regions');

            $table->index(['agent_id']);
            $table->index(['province_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_addresses');
    }
};
