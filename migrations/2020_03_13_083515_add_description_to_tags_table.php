<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescriptionToTagsTable extends Migration {

    public function up()
    {
        Schema::table('tags', function ($table) {
            $table->text('description')->nullable();
        });

    }


    public function down()
    {
        Schema::table('tags', function ($table) {
            $table->dropColumn('description');
        });
    }
}
