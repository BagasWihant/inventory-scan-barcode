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
        Schema::create('master_wip', function (Blueprint $table) {
            $table->id();
            $table->string('model',50);
            $table->string('dc',15);
            $table->string('line',10);
            $table->string('cust',10);
            $table->string('tanggal',15);
            $table->integer('qty');
            $table->string('created_by',30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_wip');
    }
};
