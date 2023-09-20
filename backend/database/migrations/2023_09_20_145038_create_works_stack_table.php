<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('works_stack', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id');
            $table->unsignedBigInteger('stack_id');

            $table->foreign('work_id')->references('id')->on('works');
            $table->foreign('stack_id')->references('id')->on('stack');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works_stack');
    }
};