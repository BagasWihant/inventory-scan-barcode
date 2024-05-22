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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('pallet_no',32);
            $table->string('material_no',32);
            $table->integer('picking_qty');
            // $table->char('is_scanned',1)->default(0);
            $table->timestamps();

            $table->foreign('pallet_barcode')->references('pallet_barcode')->on('pallets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
