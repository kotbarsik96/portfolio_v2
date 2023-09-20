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
            $table->foreignId('work_id')
                ->constrained(table: 'works')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('stack_id')
                ->constrained(table: 'works')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            ;
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