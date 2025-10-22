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
            $table->dateTime('stored_at')->nullable()->after('picking_qty');
            $table->string('stored_by',3)->nullable()->after('picking_qty');
            $table->integer('is_stored')->default(0)->after('picking_qty');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_in_stock', function (Blueprint $table) {
            $table->dropColumn('stored_at');
            $table->dropColumn('stored_by');
            $table->dropColumn('is_stored');
        });
    }
};
