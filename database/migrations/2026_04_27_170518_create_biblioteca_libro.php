<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('biblioteca_libro', function (Blueprint $table) {
            $table->id();

            $table->foreignId('biblioteca_id')->constrained()->cascadeOnDelete();
            $table->foreignId('libro_id')->constrained()->cascadeOnDelete();

            $table->enum('estado_lectura', ['pendiente','leyendo','leido'])->nullable();
            $table->date('fecha_finalizacion')->nullable();

            $table->timestamps();

            $table->unique(['biblioteca_id', 'libro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('biblioteca_libro');
    }
};
