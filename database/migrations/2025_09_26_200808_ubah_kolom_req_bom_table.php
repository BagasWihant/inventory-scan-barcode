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
        Schema::table('mps_detail',function (Blueprint $table){
            $table->double('req_bom')->change();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mps_detail',function (Blueprint $table){
            $table->integer('req_bom')->change();
        });
    
    }
};
