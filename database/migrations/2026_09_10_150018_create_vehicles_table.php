<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_code')->unique();
            $table->string('plate_number')->unique();
            $table->string('vehicle_type');
            $table->string('capacity')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
