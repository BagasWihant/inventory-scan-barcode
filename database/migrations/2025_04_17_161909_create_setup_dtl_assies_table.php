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
        Schema::create('setup_dtl_assy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setup_id')->index();
            $table->string('material_no',100)->index();
            $table->integer('qty');
            $table->string('pallet_no',32);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_dtl_assy');
    }
};
