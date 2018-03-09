<?php namespace FireUnion\BlogFront\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddAllowImages extends Migration {

	public function up() {
		Schema::table('fireunion_blogfront_authors', function ($table) {
			$table->integer('allow_images')->unsigned()->nullable()->after('can_publish');
		});
	}

	public function down() {
		Schema::table('fireunion_blogfront_authors', function ($table) {
			$table->dropColumn('allow_images');
		});
	}

}
