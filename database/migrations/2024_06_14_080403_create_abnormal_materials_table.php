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
        Schema::create('abnormal_materials', function (Blueprint $table) {
            $table->id();
            $table->string('pallet_no',32)->index();
            $table->string('material_no',32)->index();
            $table->integer('picking_qty');
            $table->bigInteger('user_id');
            $table->char('status',1)->comment('0=kurang,1=lebih');
            $table->string('locate',50)->nullable();
            $table->string('trucking_id',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abnormal_materials');
    }
};
