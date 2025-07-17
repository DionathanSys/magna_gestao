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
        Schema::create('planos_preventivo', function (Blueprint $table) {
            $table->id();
            $table->string('descricao')
                ->unique();
            $table->string('periodicidade');
            $table->unsignedInteger('intervalo')
                ->default(0);
            $table->boolean('is_active')
                ->default(true);
            $table->json('itens')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos_preventivo');
    }
};
