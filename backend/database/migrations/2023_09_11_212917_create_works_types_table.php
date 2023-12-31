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
        Schema::create('works_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')
                ->constrained(table: 'works')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('type_id')
                ->constrained(table: 'types')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('works_types');
    }
};