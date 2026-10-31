<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_bonus_payouts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('bank_account_id');
            $table->decimal('amount', 15, 2);

            $table->enum('status', [
                'pending',
                'processed',
                'failed'
            ])->default('pending');

            $table->string('transaction_ref', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();

            $table->timestamps();

            $table->foreign('agent_id')
                ->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('bank_account_id')
                ->references('id')->on('agent_bank_accounts')->onDelete('restrict');
            $table->foreign('processed_by')
                ->references('id')->on('users')->onDelete('set null');

            $table->index(['agent_id', 'status']);
            $table->index('status');
            $table->index('paid_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_bonus_payouts');
    }
};
