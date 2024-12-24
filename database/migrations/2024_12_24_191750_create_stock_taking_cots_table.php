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
        Schema::create('stock_taking_cots', function (Blueprint $table) {
            $table->id();
            $table->string('no_sto',50);
            $table->date('tgl_sto');
            $table->integer('user_id');
            $table->string('line_code',50);
            $table->string('material_no',50);
            $table->integer('qty');
            $table->date('issue_date')->nullable();
            $table->string('palet_no',50)->nullable();
            $table->string('location',50)->nullable();
            $table->string('status',1)->default(0);
            // $table->string('',50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_taking_cots');
    }
};
