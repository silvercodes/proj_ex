<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWelcomeBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('welcome_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kindergarten_id');
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('welcome_blocks');
    }
}
