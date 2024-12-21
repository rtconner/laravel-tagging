<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tagging_tags', function (Blueprint $table) {
            $table->string('locale', 5)->nullable();
        });
    }

    public function down()
    {
        Schema::table('tagging_tags', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
