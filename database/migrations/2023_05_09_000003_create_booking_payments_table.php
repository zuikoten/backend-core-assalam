<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->bigIncrements('id');

            // 1 booking = 1 payment record (bayar penuh di awal)
            $table->unsignedBigInteger('booking_id')->unique();

            $table->decimal('amount', 15, 2);

            $table->enum('payment_method', [
                'transfer',
                'virtual_account',
                'cash',
            ])->default('transfer');

            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('transaction_ref', 100)->nullable();

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'refunded',
            ])->default('pending');

            $table->string('proof_of_payment')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('booking_id')
                ->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('confirmed_by')
                ->references('id')->on('users')->onDelete('set null');

            $table->index('status');
            $table->index('paid_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_payments');
    }
};
