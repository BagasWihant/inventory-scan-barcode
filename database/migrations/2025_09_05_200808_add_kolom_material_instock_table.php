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
        Schema::table('material_in_stock',function (Blueprint $table){
            $table->string('box',1)->nullable();
        });
        Schema::table('abnormal_materials',function (Blueprint $table){
            $table->string('box',1)->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_in_stock',function (Blueprint $table){
            $table->dropColumn('box');
        });
        Schema::table('abnormal_materials',function (Blueprint $table){
            $table->dropColumn('box');
        });
    
    }
};
