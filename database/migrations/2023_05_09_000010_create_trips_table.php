<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('trip_code', 50)->unique()->nullable();

            $table->unsignedBigInteger('trip_category_id');
            $table->unsignedBigInteger('trip_category_service_id');

            $table->integer('duration_days');
            $table->integer('duration_nights');

            $table->enum('trip_schedule', ['fixed', 'recurring', 'other'])->default('fixed');

            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->integer('quota_total')->nullable();
            $table->unsignedBigInteger('team_leader_id')->nullable();

            $table->date('open_from')->nullable();
            $table->date('open_until')->nullable();

            $table->string('overview_title')->nullable();
            $table->longText('description')->nullable();

            $table->string('highlights_title')->nullable();
            $table->json('highlights')->nullable();

            $table->string('include_title')->nullable();
            $table->json('includes')->nullable();

            $table->string('exclude_title')->nullable();
            $table->json('excludes')->nullable();

            $table->string('map_title')->nullable();
            $table->string('map_image')->nullable();
            $table->text('map_iframe')->nullable();

            $table->json('gallery')->nullable();
            $table->json('video_urls')->nullable();

            $table->string('faq_title')->nullable();
            $table->json('faq')->nullable();

            $table->enum('status', [
                'draft',
                'open',
                'full',
                'closed',
                'ongoing',
                'completed',
                'cancelled',
            ])->default('draft');

            $table->boolean('is_published')->default(false);
            $table->string('thumbnail')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trip_category_id')
                ->references('id')->on('trip_categories')->onDelete('restrict');
            $table->foreign('trip_category_service_id')
                ->references('id')->on('trip_category_services')->onDelete('restrict');
            $table->foreign('team_leader_id')
                ->references('id')->on('staff')->onDelete('set null');
            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('set null');

            $table->index('slug');
            $table->index('trip_code');
            $table->index(['trip_category_id', 'status']);
            $table->index(['trip_category_service_id', 'status']);
            $table->index(['status', 'is_published']);
            $table->index('departure_date');
            $table->index('trip_schedule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
