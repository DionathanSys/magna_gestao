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
        Schema::create('anotacoes_viagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viagem_id')->constrained('viagens')->cascadeOnDelete();
            $table->string('descricao', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anotacoes_viagem');
    }
};
