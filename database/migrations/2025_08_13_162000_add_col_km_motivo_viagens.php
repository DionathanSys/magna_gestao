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
            $table->decimal('km_motivo_dispersao', 10, 2)
                ->nullable();
            // $table

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viagens', function (Blueprint $table) {
            $table->dropColumn('km_motivo_dispersao');
        });
    }
};
