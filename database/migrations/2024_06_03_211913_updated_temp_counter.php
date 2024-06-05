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
        Schema::table('temp_counters', function (Blueprint $table) {
            $table->integer('qty_more')->default(0);
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_counters', function (Blueprint $table) {
            $table->dropColumn('qty_more');
        });
    }
};
