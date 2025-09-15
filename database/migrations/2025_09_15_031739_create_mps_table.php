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
        Schema::create('mps', function (Blueprint $table) {
            $table->id();
            $table->string('product_no',60);
            $table->string('cusdesch_c1',2);
            $table->string('cusdesch_c2',2);
            $table->string('cusdesch_c3',2);
            $table->integer('lot_no');
            $table->string('assy_section_cd',8);
            $table->string('line_c',5);
            $table->date('plan_issue_dt');
            $table->integer('plan_issue_qty');
            $table->string('kit_no',25);
            $table->date('issue_dt');
            $table->date('entry_dt');
            $table->string('entry_by',20);
        });
        Schema::create('mps_detail', function (Blueprint $table) {
            $table->id();
            $table->string('kit_no',32);
            $table->string('material_no',32);
            $table->string('location_c',4)->nullable();
            $table->integer('req_bom',2);
            $table->integer('req_issue')->nullable();
            $table->string('remain',8)->nullable();
            $table->date('issue_dt');
            $table->date('entry_dt');
            $table->string('issue_by',20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mps');
        Schema::dropIfExists('mps_detail');
    }
};
