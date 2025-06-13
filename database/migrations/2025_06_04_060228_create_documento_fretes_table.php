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
        Schema::create('documentos_frete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->foreignId('integrado_id')->nullable()->constrained('integrados');
            $table->string('numero_documento', 50)->nullable();
            $table->string('documento_transporte', 50)->nullable();
            $table->string('tipo_documento', 20)->nullable();
            $table->date('data_emissao');
            $table->decimal('valor_total', 14)->default(0);
            $table->decimal('valor_icms', 14)->default(0);
            $table->string('municipio')->nullable();
            $table->string('estado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_frete');
    }
};
