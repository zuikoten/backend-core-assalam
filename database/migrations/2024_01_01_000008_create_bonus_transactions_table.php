<?php
// database/migrations/2024_01_01_000008_create_bonus_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bonus_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->integer('pilgrim_count')->default(1);
            $table->enum('bonus_type', ['fixed_fee', 'coaching_fee', 'region_bonus']);
            $table->decimal('amount', 15, 2);
            $table->string('description', 500)->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('payment_id')->nullable();

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments');

            $table->index(['agent_id']);
            $table->index(['bonus_type']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bonus_transactions');
    }
};
