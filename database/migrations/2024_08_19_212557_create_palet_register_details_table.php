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
        Schema::create('palet_register_details', function (Blueprint $table) {
            $table->id();
            $table->string('palet_no',32);
            $table->string('material_no',50);
            $table->string('material_name',50);
            $table->integer('qty');
            $table->char('is_done',1)->default(0);
            $table->integer('user_id');
            $table->timestamps();

            $table->foreign('palet_no')->references('palet_no')->on('palet_registers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palet_register_details');
    }
};
