<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDocumentGroupsTableAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_groups', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('title_ru')->after('title')->nullable();
            $table->string('title_ua')->after('title_ru')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_groups', function (Blueprint $table) {
            $table->string('title')->change();
            $table->dropColumn('title_ru');
            $table->dropColumn('title_ua');
        });
    }
}
