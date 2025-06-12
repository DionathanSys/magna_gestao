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
        Schema::create('divergencias_viagem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viagem_id')->constrained('viagens')->cascadeOnDelete();
            $table->string('tipo_divergencia', 50);
            $table->string('descricao', 255)->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divergencias_viagem');
    }
};
