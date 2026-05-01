<?php
// database/migrations/2024_01_01_000007_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->enum('payment_type', ['registration_fee', 'bonus_fee', 'other']);
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['transfer', 'cash', 'virtual_account'])->default('transfer');
            $table->string('bank_account')->nullable();
            $table->string('transaction_ref', 100)->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->unsignedBigInteger('proof_document_id')->nullable();

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('proof_document_id')->references('id')->on('agent_documents');

            $table->index(['agent_id']);
            $table->index(['status']);
            $table->index(['payment_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
