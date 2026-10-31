<?php
// database/migrations/2024_01_01_000006_create_agent_bank_accounts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->string('bank_name', 100);
            $table->string('account_number', 50);
            $table->string('account_holder', 255);
            $table->boolean('is_primary')->default(true);

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');

            $table->index(['agent_id']);
            $table->index(['account_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_bank_accounts');
    }
};
