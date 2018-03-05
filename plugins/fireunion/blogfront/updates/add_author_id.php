<?php namespace FireUnion\BlogFront\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddAuthorId extends Migration {

	public function up() {
		Schema::table('rainlab_blog_posts', function ($table) {
			$table->integer('author_id')->unsigned()->nullable()->after('user_id');
		});
	}

	public function down() {
		Schema::table('rainlab_blog_posts', function ($table) {
			$table->dropColumn('author_id');
		});
	}

}
