<?php
// database/migrations/2024_01_01_000001_create_regions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('region_type', ['province', 'regency', 'district']);
            $table->string('code', 10)->unique();
            $table->string('name', 255);
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('regions')->onDelete('set null');
            $table->index(['parent_id']);
            $table->index(['code']);
            $table->index(['region_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('regions');
    }
};
