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
        Schema::create('recapagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pneu_id')
                ->constrained('pneus')
                ->cascadeOnDelete();
            $table->date('data_recapagem');
            $table->foreignId('desenho_pneu_id')
                ->constrained('desenhos_pneu')
                ->cascadeOnDelete();
            $table->foreignId('parceiro_id')
                ->constrained('parceiros')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recapagens');
    }
};
