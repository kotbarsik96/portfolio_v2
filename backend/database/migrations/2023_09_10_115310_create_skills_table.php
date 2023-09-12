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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('video_id')->nullable();
            $table->timestamps();

            $table->foreign('image_id')->references('id')->on('images');
            $table->foreign('video_id')->references('id')->on('video');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skills');
    }
};