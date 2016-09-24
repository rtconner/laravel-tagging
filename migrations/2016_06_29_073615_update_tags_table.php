<?php

use Illuminate\Database\Migrations\Migration;

class UpdateTagsTable extends Migration
{

    public function up()
    {
        Schema::table('tagging_tags', function ($table) {
            $table->integer('tag_group_id')->unsigned()->nullable()->after('id');
            $table->foreign('tag_group_id')->references('id')->on('tagging_tag_groups');
        });
    }

    public function down()
    {
        Schema::table('tagging_tags', function ($table) {
            $table->dropForeign('tagging_tags_tag_group_id_foreign');
            $table->dropColumn('tag_group_id');
        });
    }
}
