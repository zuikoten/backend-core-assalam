<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_package_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trip_package_id');
            $table->unsignedBigInteger('pricing_category_id');

            $table->decimal('regular_price', 15, 2);
            $table->boolean('is_sale')->default(false);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('trip_package_id')
                ->references('id')->on('trip_packages')->onDelete('cascade');
            $table->foreign('pricing_category_id')
                ->references('id')->on('pricing_categories')->onDelete('restrict');

            $table->unique(['trip_package_id', 'pricing_category_id']);

            $table->index(['trip_package_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_package_prices');
    }
};
