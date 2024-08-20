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
        Schema::create('palet_registers', function (Blueprint $table) {
            $table->string('palet_no',32)->primary();
            $table->string('line_c',50);
            $table->char('status',1)->default(0);
            $table->char('is_done',1)->default(0);
            $table->dateTime('supply_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palet_registers');
    }
};
