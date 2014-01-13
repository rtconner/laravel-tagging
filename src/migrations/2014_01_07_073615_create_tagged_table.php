<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaggedTable extends Migration {

	public function up() {
		Schema::create('tagged', function(Blueprint $table) {
			$table->increments('id');
			$table->morphs('taggable');
			$table->string('tag_string', 60)->index();
			$table->string('tag_slug', 60)->index();
		});
	}

	public function down() {
		Schema::drop('tagged');
	}
}