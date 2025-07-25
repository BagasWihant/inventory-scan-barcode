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
        Schema::table('material_request_assy',function (Blueprint $table){
            $table->string('product_model',50)->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_request_assy',function (Blueprint $table){
            $table->dropColumn('product_model');
        });
    
    }
};
