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
        Schema::create('material_in_stock', function (Blueprint $table) {
            $table->id();
            $table->string('pallet_no',32)->index();
            $table->string('material_no',32)->index();
            $table->integer('picking_qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_in_stock');
    }
};
