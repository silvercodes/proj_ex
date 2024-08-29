<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKindergartenGroupsFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kindergarten_groups_files', function (Blueprint $table) {
            $table->unsignedBigInteger('kindergarten_group_id');
            $table->unsignedBigInteger('file_id');

            $table->primary(['kindergarten_group_id', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kindergarten_groups_files');
    }
}
