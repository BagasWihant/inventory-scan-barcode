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
        Schema::table('temp_counter_siws',function (Blueprint $table){
            $table->string('serial_no',100)->nullable();
            $table->string('material',255)->change();
        });
        
        Schema::table('material_in_stock',function (Blueprint $table){
            $table->string('material_no',255)->change();
        });
        
        Schema::table('abnormal_materials',function (Blueprint $table){
            $table->string('material_no',255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_counter_siws',function (Blueprint $table){
            $table->dropColumn('serial_no');
        });
    
    }
};
