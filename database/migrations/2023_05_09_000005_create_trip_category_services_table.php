<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_category_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trip_category_id');
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('trip_category_id')
                ->references('id')->on('trip_categories')->onDelete('cascade');

            $table->index(['trip_category_id', 'is_active']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_category_services');
    }
};
