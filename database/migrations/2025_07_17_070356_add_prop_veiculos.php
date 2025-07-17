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
        Schema::table('veiculos', function (Blueprint $table) {
            $table->string('modelo')
                ->nullable()
                ->after('placa');
            $table->string('marca')
                ->nullable()
                ->after('modelo');
            $table->decimal('ano_fabricacao', 4, 0)
                ->nullable()
                ->after('marca');
            $table->decimal('ano_modelo', 4, 0)
                ->nullable()
                ->after('ano_fabricacao');
            $table->string('chassis')
                ->nullable()
                ->after('ano_modelo');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropColumn(['modelo', 'marca', 'ano_fabricacao', 'ano_modelo']);
        });
    }
};
