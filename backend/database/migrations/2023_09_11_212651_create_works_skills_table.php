<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')
                ->constrained(table: 'works')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('skill_id')
                ->constrained(table: 'skills')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->longText('description')->nullable();
            $table->string('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('works_skills');
    }
};