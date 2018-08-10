<?php namespace Wwrf\TransitionCenter\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateRainlabBlogEmailField extends Migration
{
    public function up()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->string('email')->nullabe();
        });
    }

    public function down()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->dropColumn('email');
        });
    }
}
