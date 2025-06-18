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
        Schema::create('desenhos_pneu', function (Blueprint $table) {
            $table->id();
            $table->string('medida')->nullable();
            $table->string('modelo')->nullable();
            $table->string('estado_pneu');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desenhos_pneu');
    }
};
