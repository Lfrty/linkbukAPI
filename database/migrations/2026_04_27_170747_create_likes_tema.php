<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('likes_tema', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('tema_id')->constrained('temas_foro')->cascadeOnDelete();

            $table->primary(['usuario_id', 'tema_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('likes_tema');
    }
};
