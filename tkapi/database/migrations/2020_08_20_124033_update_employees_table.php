<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function(Blueprint $table) {
                $table->string('education')->nullable()->after('position');
                $table->integer('teaching_experience')->nullable()->after('education');
                $table->integer('management_experience')->nullable()->after('teaching_experience');
                $table->string('awards')->nullable()->after('management_experience');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function(Blueprint $table) {
            $table->dropColumn('education');
            $table->dropColumn('teaching_experience');
            $table->dropColumn('management_experience');
            $table->dropColumn('awards');
        });
    }
}
