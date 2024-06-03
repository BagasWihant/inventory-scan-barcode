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
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->char('stat',1)->default(0)->after('picking_qty')->comment('1 = lebih, N = item baru tidak ada di db, 0 = normal')->index();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->dropIndex('material_in_stock_stat_index');
            $table->dropColumn('stat');
        });
    }
};
