<?php
// database/migrations/2024_01_01_000007_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');

            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', [
                'transfer',
                'cash',
                'virtual_account'
            ])->default('transfer');

            $table->unsignedBigInteger('bank_account_id')->nullable();

            $table->string('transaction_ref', 100)->nullable();
            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'refunded'
            ])->default('pending');

            $table->timestamp('paid_at')->nullable();

            $table->unsignedBigInteger('proof_document_id')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('agent_id')
                ->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('bank_account_id')
                ->references('id')->on('agent_bank_accounts')->onDelete('set null');
            $table->foreign('proof_document_id')
                ->references('id')->on('agent_documents')->onDelete('set null');

            $table->index(['agent_id']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_payments');
    }
};
