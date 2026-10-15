<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix');
            $table->string('date');
            $table->unsignedInteger('counter')->default(0);
            $table->timestamps();

            $table->unique(['prefix', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};
