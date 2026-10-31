<?php
// database/migrations/2024_01_01_000005_create_agent_documents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->enum('document_type', ['ktp_photo', 'selfie_ktp', 'transfer_proof']);
            $table->string('file_path', 500);
            $table->integer('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->unique(['agent_id', 'document_type']);

            $table->index(['agent_id', 'document_type']);
            $table->index(['verification_status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_documents');
    }
};
