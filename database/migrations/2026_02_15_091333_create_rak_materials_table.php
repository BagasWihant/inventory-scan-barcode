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
        Schema::create('rak_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rak_id');
            $table->string('nama',100);
            $table->string('kode',100);
            $table->string('satuan',5);
            $table->integer('stok');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rak_material');
    }
};
