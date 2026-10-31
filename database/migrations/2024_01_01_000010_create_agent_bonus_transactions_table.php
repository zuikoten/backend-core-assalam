<?php
// database/migrations/2024_01_01_000008_create_bonus_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_bonus_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('pilgrim_id')->nullable();
            $table->unsignedBigInteger('from_agent_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();

            $table->enum('bonus_type', ['fixed_fee', 'coaching_fee', 'region_bonus']);
            $table->decimal('amount', 15, 2);
            $table->string('description', 500)->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

            $table->unsignedBigInteger('payout_id')->nullable();

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('pilgrim_id')->references('id')->on('pilgrim_profiles')->onDelete('set null');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            $table->foreign('from_agent_id')->references('id')->on('agents')->onDelete('set null');
            $table->foreign('payout_id')->references('id')->on('agent_bonus_payouts')->onDelete('set null');

            $table->index(['agent_id', 'status']);
            $table->index(['bonus_type']);
            $table->index(['pilgrim_id']);
            $table->index(['payout_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_bonus_transactions');
    }
};
