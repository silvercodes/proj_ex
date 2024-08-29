<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tt_group_id');
            $table->unsignedBigInteger('tt_day_id');
            $table->unsignedBigInteger('tt_part_id');
            $table->string('subjects', 2000);
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
        Schema::dropIfExists('tts');
    }
}
