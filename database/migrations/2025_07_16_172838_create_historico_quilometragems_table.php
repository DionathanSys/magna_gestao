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
        Schema::create('historico_quilometragens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')
                ->nullable()
                ->constrained('veiculos')
                ->nullOnDelete();
            $table->dateTime('data_referencia');
            $table->integer('quilometragem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_quilometragens');
    }
};
