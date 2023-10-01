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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->foreignId('tag_id')
                ->constrained(table: 'tags')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('image_desktop_id')
                ->nullable()
                ->constrained(table: 'images')
                ->onUpdate('cascade')
                ->nullOnDelete();
            $table->foreignId('image_mobile_id')
                ->nullable()
                ->constrained(table: 'images')
                ->onUpdate('cascade')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('works');
    }
};