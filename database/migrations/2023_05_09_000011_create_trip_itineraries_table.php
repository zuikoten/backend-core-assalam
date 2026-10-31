<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_itineraries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trip_id');

            $table->string('day_label', 20);
            $table->string('title');
            $table->longText('description');
            $table->integer('sort_order')->default(1);

            $table->timestamps();

            $table->foreign('trip_id')
                ->references('id')->on('trips')->onDelete('cascade');

            $table->index(['trip_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_itineraries');
    }
};
