<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('respuestas_foro', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tema_id')->constrained('temas_foro')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();

            $table->text('contenido');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('respuestas_foro');
    }
};
