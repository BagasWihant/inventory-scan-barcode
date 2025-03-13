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
        Schema::create('temp_counter_siws', function (Blueprint $table) {
            $table->id();
            $table->string('material',32)->index();
            $table->string('palet',32)->index();
            $table->bigInteger('userID')->index();
            $table->integer('total')->default(0);
            $table->integer('counter')->default(0);
            $table->integer('sisa');
            $table->integer('pax');
            $table->integer('qty_more')->default(0);
            $table->json('prop_ori')->nullable();
            $table->json('prop_scan')->nullable();
            $table->integer('scan_count')->default(0);
            $table->char('flag',1)->comment('0=palet,1=po')->default(0);
            $table->string('line_c', 50)->nullable();
            $table->string('kit_no', 32)->nullable();
            $table->dateTime('scanned_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_counter_siws');
    }
};
