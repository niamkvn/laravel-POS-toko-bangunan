<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('price_level_id')->nullable()->constrained('price_levels')->cascadeOnDelete();
            $table->decimal('min_qty', 15, 3)->default(1);
            $table->decimal('price', 15, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'price_level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
