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
        Schema::create('material_in_stock_assy', function (Blueprint $table) {
            $table->id();
            
            
            $table->string('transaksi_no',50)->index();
            $table->string('material_no',50)->index();
            $table->string('material_name',50);
            $table->char('type',1)->comment('0=reguler,1=retur')->nullable();
            $table->integer('qty');
            $table->date('issue_date')->nullable();
            $table->string('line_c',50)->nullable();
            $table->integer('iss_min_lot')->nullable();
            $table->string('iss_unit',50)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->char('status',1)->default(0);
            $table->string('loc_cd',50)->nullable();
            $table->dateTime('proses_date')->nullable();
            $table->string('surat_jalan',100)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_in_stock_assy');
    }
};
