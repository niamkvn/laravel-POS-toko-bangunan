<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('customer_type', ['umum', 'tukang', 'reseller', 'kontraktor', 'developer', 'perusahaan'])->default('umum');
            $table->foreignId('price_level_id')->nullable()->constrained('price_levels')->nullOnDelete();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->unsignedInteger('payment_term_days')->default(0);
            $table->string('tax_number')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
