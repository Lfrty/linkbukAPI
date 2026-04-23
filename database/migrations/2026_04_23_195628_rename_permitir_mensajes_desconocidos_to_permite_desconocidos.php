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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->renameColumn(
                'permitir_mensajes_desconocidos',
                'permite_desconocidos'
            );
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->renameColumn(
                'permite_desconocidos',
                'permitir_mensajes_desconocidos'
            );
        });
    }
};
