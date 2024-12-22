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
        Schema::create('temp_requests', function (Blueprint $table) {
            $table->id();
            $table->string('transaksi_no',100);
            $table->string('material_no',100);
            $table->integer('qty_request');
            $table->integer('qty_supply');
            $table->foreignId('user_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_requests');
    }
};
