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
        Schema::create('material_request_assy', function (Blueprint $table) {
            $table->id();
            $table->string('transaksi_no',50)->index();
            $table->string('material_no',50)->index();
            $table->string('material_name',50);
            $table->char('type',1)->comment('0=reguler,1=urgent');
            $table->integer('request_qty');
            $table->integer('bag_qty');
            $table->date('issue_date')->nullable();
            $table->string('line_c',50)->nullable();
            $table->integer('iss_min_lot');
            $table->string('iss_unit',50);
            $table->unsignedBigInteger('user_id');
            $table->string('user_request',50)->nullable();
            $table->char('status',1)->default(0);
            $table->string('loc_cd',50)->nullable();
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
