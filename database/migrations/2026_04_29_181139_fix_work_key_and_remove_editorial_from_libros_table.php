<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('libros', function (Blueprint $table) {
            $table->string('work_key')->nullable()->unique()->change();
            $table->dropColumn('editorial');
        });
        DB::table('libros')
            ->where('work_key', '')
            ->update(['work_key' => null]);


    }

    public function down(): void {
        Schema::table('libros', function (Blueprint $table) {
            $table->string('editorial')->nullable();
            $table->string('work_key')->nullable(false)->change();
        });
    }
};
