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
        Schema::create('real_stock_takings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sto_id')->index();
            $table->bigInteger('user_id')->index();
            $table->string('material_no',32);
            $table->string('loc_sys',50);
            $table->integer('qty_sys');
            $table->string('loc_sto',50);
            $table->integer('qty_sto');
            $table->integer('result_qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_stock_takings');
    }
};
