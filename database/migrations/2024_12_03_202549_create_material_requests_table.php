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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('transaksi_no',50)->index();
            $table->string('material_no',50)->index();
            $table->string('material_name',50);
            $table->char('type',1)->comment('0=reguler,1=urgent');
            $table->integer('request_qty');
            $table->string('request_user',50)->nullable();
            $table->integer('bag_qty');
            $table->integer('iss_min_lot');
            $table->unsignedBigInteger('created_by');
            $table->char('status',1)->default(0);
            $table->dateTime('proses_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
