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
        Schema::create('gestor_indicador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestor_id')
                ->constrained('gestores');
            $table->foreignId('indicador_id')
                ->constrained('indicadores');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestor_indicador');
    }
};
