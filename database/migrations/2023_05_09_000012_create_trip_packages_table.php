<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trip_id');

            $table->string('name', 100);
            $table->enum('room_type', ['single', 'double', 'triple', 'quad']);
            $table->integer('min_pax')->default(1);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('trip_id')
                ->references('id')->on('trips')->onDelete('cascade');

            $table->index(['trip_id', 'is_active']);
            $table->index('room_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_packages');
    }
};
