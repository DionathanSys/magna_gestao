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
        Schema::create('pneus', function (Blueprint $table) {
            $table->id();
            $table->string('numero_fogo')->unique();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('medida')->nullable();
            $table->string('desenho_pneu_id')->nullable();
            $table->string('status');
            $table->string('local')->nullable();
            $table->date('data_aquisicao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pneus');
    }
};
