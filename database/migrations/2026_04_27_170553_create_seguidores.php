<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('seguidores', function (Blueprint $table) {
            $table->foreignId('seguidor_id')->constrained('usuarios');
            $table->foreignId('seguido_id')->constrained('usuarios');
            $table->timestamps();

            $table->primary(['seguidor_id', 'seguido_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('seguidores');
    }
};
