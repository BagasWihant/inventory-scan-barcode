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
        Schema::create('master_sto', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->char('status',1)->nullable();
            $table->timestamp('date_start')->default(now());
            $table->timestamp('date_end')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sto');
    }
};
