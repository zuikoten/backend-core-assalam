<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trip_id');

            $table->string('batch_name')->nullable();
            $table->date('departure_date');
            $table->date('return_date');
            $table->integer('quota_total');
            $table->unsignedBigInteger('team_leader_id')->nullable();

            $table->enum('status', ['open', 'full', 'closed', 'ongoing', 'completed', 'cancelled'])
                ->default('open');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('trip_id')
                ->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('team_leader_id')
                ->references('id')->on('staff')->onDelete('set null');

            $table->index(['trip_id', 'status']);
            $table->index('departure_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_batches');
    }
};
