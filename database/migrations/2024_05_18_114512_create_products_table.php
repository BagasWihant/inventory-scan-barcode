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
            $table->string('pallet_barcode',32);
            $table->string('product_barcode',32);
            $table->string('product_name',50);
            $table->text('stock');
            $table->char('status',1)->default(0);
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
