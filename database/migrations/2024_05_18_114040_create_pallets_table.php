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
        Schema::create('pallets', function (Blueprint $table) {
            $table->id();
            $table->string('pallet_barcode',32)->unique();
            $table->string('line',32);
            $table->string('pallet_serial',32)->comment('id + line');
            $table->string('trucking_id',20);
            $table->boolean('is_scanned')->default(0);
            $table->string('scanned_by',20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallets');
    }
};
