<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('image_desktop_id')->nullable();
            $table->unsignedBigInteger('image_mobile_id')->nullable();
            $table->timestamps();

            $table->foreign('tag_id')->references('id')->on('tags');
            $table->foreign('image_desktop_id')->references('id')->on('images');
            $table->foreign('image_mobile_id')->references('id')->on('images');
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
