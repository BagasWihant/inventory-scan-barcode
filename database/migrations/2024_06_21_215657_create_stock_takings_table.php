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
        Schema::create('stock_takings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sto_id')->index();
            $table->bigInteger('user_id')->index();
            $table->string('material_no',32);
            $table->char('hitung',1);
            $table->string('loc',50);
            $table->integer('qty');
            $table->char('is_taking',1)->default(0)->comment('0=awal,1=confirm');
            $table->timestamps();

            $table->foreign('sto_id')->references('id')->on('master_sto')->onDelete('cascade');
            $table->index(['sto_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_takings');
    }
};
