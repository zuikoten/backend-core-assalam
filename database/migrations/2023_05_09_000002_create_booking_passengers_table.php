<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id');

            $table->unsignedBigInteger('pilgrim_profile_id')->nullable();

            $table->boolean('is_primary')->default(false);

            $table->unsignedBigInteger('pricing_category_id');

            $table->string('full_name');
            $table->string('nik', 16)->nullable();
            $table->string('passport_number', 20)->nullable();
            $table->date('passport_expiry')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('phone', 20)->nullable();

            $table->string('foto_ktp')->nullable();
            $table->string('foto_passport')->nullable();

            $table->decimal('price', 15, 2);

            $table->enum('document_status', [
                'incomplete',
                'complete',
                'verified',
            ])->default('incomplete');

            $table->timestamps();

            $table->foreign('booking_id')
                ->references('id')->on('bookings')->onDelete('cascade');

            $table->foreign('pilgrim_profile_id')
                ->references('id')->on('pilgrim_profiles')->onDelete('set null');

            $table->index('booking_id');
            $table->index('pilgrim_profile_id');
            $table->index('document_status');
            $table->index('is_primary');
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_passengers');
    }
};
