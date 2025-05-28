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
        Schema::create('kahoot_games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre_concurso')->index();
            $table->date('fecha_celebracion');
            $table->integer('numero_participantes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kahoot_games');
    }
};
