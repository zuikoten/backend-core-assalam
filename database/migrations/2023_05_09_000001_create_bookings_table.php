<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nomor booking unik untuk display ke customer
            // Format: BK-YYYYMMDD-XXXX  contoh: BK-20260501-A3F2
            $table->string('booking_number', 20)->unique();

            // Relasi ke Modul Trip (cross-module, tanpa FK)
            $table->unsignedBigInteger('trip_id');

            $table->unsignedBigInteger('trip_batch_id')->nullable();
            // NULL = trip fixed
            // Terisi = trip recurring

            $table->unsignedBigInteger('package_id');

            // Pemesan utama (user)
            $table->unsignedBigInteger('user_id');

            // Via agen
            $table->unsignedBigInteger('agent_id')->nullable();

            // Ringkasan
            $table->integer('total_pax');
            $table->decimal('total_amount', 15, 2);

            $table->enum('status', [
                'pending',
                'confirmed',
                'departed',
                'returned',
                'cancelled',
            ])->default('pending');

            $table->enum('document_status', [
                'incomplete',
                'complete',
                'verified',
            ])->default('incomplete');

            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Untuk booking via agen, ini diisi user_id si agen
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')->on('users')->onDelete('restrict');
            $table->foreign('agent_id')
                ->references('id')->on('agents')->onDelete('set null');
            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('set null');

            $table->index(['trip_id', 'status']);
            $table->index(['trip_batch_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('agent_id');
            $table->index('booking_number');
            $table->index('document_status');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
