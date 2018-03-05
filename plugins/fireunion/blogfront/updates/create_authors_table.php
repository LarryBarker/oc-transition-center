<?php namespace FireUnion\BlogFront\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateAuthorsTable extends Migration {
	public function up() {
		Schema::create('fireunion_blogfront_authors', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('admin_id')->unsigned();
			$table->string('categories');
			$table->boolean('can_publish')->default(false);
			$table->string('note');
			$table->timestamps();
		});
	}

	public function down() {
		Schema::dropIfExists('fireunion_blogfront_authors');
	}
}
