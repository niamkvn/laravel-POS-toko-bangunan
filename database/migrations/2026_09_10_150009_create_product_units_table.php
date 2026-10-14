<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('conversion_factor', 15, 4)->default(1);
            $table->boolean('is_base')->default(false);
            $table->boolean('is_purchase_unit')->default(false);
            $table->boolean('is_sale_unit')->default(true);
            $table->string('barcode')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
