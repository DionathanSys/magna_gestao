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
            $table->decimal('km_dispersao', 10, 2)
                ->virtualAs("COALESCE(km_rodado, 0) - COALESCE(km_pago, 0)");
            $table->decimal('dispersao_percentual', 10, 2)
                ->virtualAs("(COALESCE(km_dispersao, 0) / NULLIF(COALESCE(km_rodado, 0), 0)) * 100");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viagens', function (Blueprint $table) {
            $table->dropColumn('dispersao_percentual');
            $table->dropColumn('km_dispersao');
        });
    }
};
