<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_difficulty', function (Blueprint $table) {
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('difficulty_id');
            $table->primary(['trip_id', 'difficulty_id']);

            $table->foreign('trip_id')
                ->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('difficulty_id')
                ->references('id')->on('difficulties')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_difficulty');
    }
};
