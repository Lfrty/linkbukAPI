<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('libro_id')->constrained()->cascadeOnDelete();

            $table->integer('puntuacion');
            $table->text('comentario')->nullable();

            $table->timestamps();

            $table->unique(['usuario_id', 'libro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('resenas');
    }
};
