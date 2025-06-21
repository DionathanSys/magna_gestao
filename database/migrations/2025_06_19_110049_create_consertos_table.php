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
        Schema::create('consertos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pneu_id')
                ->constrained('pneus')
                ->cascadeOnDelete();

            $table->date('data_conserto');

            $table->string('tipo_conserto');

            $table->foreignId('parceiro_id')
                ->constrained('parceiros')
                ->cascadeOnDelete();

            $table->decimal('valor', 12, 2)
                ->default(0.00);

            $table->boolean('garantia')
                ->default(true);

            $table->foreignId('veiculo_id')
                ->constrained('veiculos')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consertos');
    }
};
