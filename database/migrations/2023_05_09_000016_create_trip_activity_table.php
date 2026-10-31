<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_activity', function (Blueprint $table) {
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('activity_id');
            $table->primary(['trip_id', 'activity_id']);

            $table->foreign('trip_id')
                ->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('activity_id')
                ->references('id')->on('activities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_activity');
    }
};
