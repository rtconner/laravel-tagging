<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Stubs extends Migration {

	public function up() {
	
		Schema::create('tagging_stubs', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
		});
		
	}
	
	public function down() {
		Schema::drop('tagging_stubs');
	}
	
}
