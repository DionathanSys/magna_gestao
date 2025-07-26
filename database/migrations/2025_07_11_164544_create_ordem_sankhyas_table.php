<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ordens_sankhya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_servico_id')
                ->constrained('ordens_servico')
                ->cascadeOnDelete();
            $table->string('ordem_sankhya_id', 50)
                ->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordens_sankhya');
    }
};
