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
        Schema::table('stock_takings', function (Blueprint $table) {
            $table->unique(['sto_id', 'material_no', 'user_id','hitung'], 'unique_stock_taking');
        });
        Schema::table('temp_counters', function (Blueprint $table) {
            $table->integer('scan_count')->default(0);
        });
        
        //
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_counters', function (Blueprint $table) {
            $table->dropColumn('scan_count');
        });
        Schema::table('stock_takings', function (Blueprint $table) {
            $table->dropIndex('unique_stock_taking');
        });
        //
    }
};
