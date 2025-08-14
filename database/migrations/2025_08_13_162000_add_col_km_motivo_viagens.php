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
        Schema::table('viagens', function (Blueprint $table) {


            // $table->decimal('km_dispersao', 10, 2)
            //     ->virtualAs("COALESCE(km_rodado_excedente, 0) - COALESCE(km_cobrar, 0) - COALESCE(km_motivo_divergencia, 0)");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viagens', function (Blueprint $table) {
            $table->dropColumn('km_dispersao');
            $table->dropColumn('km_motivo_divergencia');
        });
    }
};
