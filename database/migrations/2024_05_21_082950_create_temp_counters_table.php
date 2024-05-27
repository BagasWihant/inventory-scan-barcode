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
        Schema::create('temp_counters', function (Blueprint $table) {
            $table->string('material',32)->index();
            $table->string('material_fix',32)->index();
            $table->string('palet',32)->index();
            $table->bigInteger('userID')->index();
            $table->integer('total')->default(0);
            $table->integer('counter')->default(0);
            $table->integer('sisa');
            $table->integer('pax');
            $table->unique(['material','palet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_counters');
    }
};
