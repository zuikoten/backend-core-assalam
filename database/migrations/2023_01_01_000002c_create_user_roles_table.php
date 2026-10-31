<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable(); // user_id yang assign
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['user_id', 'role_id']); // 1 user tidak bisa punya role sama 2x
            $table->index('user_id');
            $table->index('role_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
};
