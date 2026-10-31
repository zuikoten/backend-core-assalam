<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_destination', function (Blueprint $table) {
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('destination_id');
            $table->primary(['trip_id', 'destination_id']);

            $table->foreign('trip_id')
                ->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('destination_id')
                ->references('id')->on('destinations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_destination');
    }
};
