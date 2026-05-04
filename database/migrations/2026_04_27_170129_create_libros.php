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

            $table->string('work_key')->unique();

            $table->string('autor')->nullable();

            $table->integer('anyo_publicacion')->nullable();

            $table->text('descripcion')->nullable();

            $table->string('portada')->nullable();

            $table->integer('paginas')->nullable()->unique();

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
