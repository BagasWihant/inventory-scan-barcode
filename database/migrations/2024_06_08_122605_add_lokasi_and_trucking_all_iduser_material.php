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
            $table->bigInteger('user_id')->after('picking_qty');
            $table->string('locate',50)->nullable()->after('picking_qty');
            $table->string('trucking_id',50)->nullable()->after('picking_qty');
        });
        
        Schema::table('material_kelebihans', function (Blueprint $table) {
            $table->bigInteger('user_id')->after('picking_qty');
            $table->string('locate',50)->nullable()->after('picking_qty');
            $table->string('trucking_id',50)->nullable()->after('picking_qty');
        });
        
        Schema::table('material_kurang', function (Blueprint $table) {
            $table->bigInteger('user_id')->after('picking_qty');
            $table->string('locate',50)->nullable()->after('picking_qty');
            $table->string('trucking_id',50)->nullable()->after('picking_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->dropColumn('locate');
            $table->dropColumn('trucking_id');
            $table->dropColumn('user_id');
        });
        Schema::table('material_kelebihans', function (Blueprint $table) {
            $table->dropColumn('locate');
            $table->dropColumn('trucking_id');
            $table->dropColumn('user_id');
        });
        Schema::table('material_kurang', function (Blueprint $table) {
            $table->dropColumn('locate');
            $table->dropColumn('trucking_id');
            $table->dropColumn('user_id');
        });
    }
};
