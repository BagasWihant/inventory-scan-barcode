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
        Schema::create('setup_mst_assy', function (Blueprint $table) {
            $table->id();
            $table->date('issue_date')->index();
            $table->string('line_cd',30);
            $table->char('status',2);
            $table->bigInteger('created_by')->index();
            $table->timestamps();
            $table->timestamp('finished_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_mst_assy');
    }
};
