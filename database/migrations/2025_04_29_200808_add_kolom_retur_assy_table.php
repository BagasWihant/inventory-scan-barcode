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
        Schema::table('retur_assy',function (Blueprint $table){
            $table->string('no_retur',100)->index();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retur_assy',function (Blueprint $table){
            $table->dropColumn('no_retur');
        });
    
    }
};
