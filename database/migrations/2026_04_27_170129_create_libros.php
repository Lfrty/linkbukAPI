<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->string('autor')->nullable();

            $table->string('isbn')->nullable()->unique();

            $table->string('editorial')->nullable();
            $table->date('fecha_publicacion')->nullable();

            $table->text('descripcion')->nullable();
            $table->string('portada')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('libros');
    }
};
