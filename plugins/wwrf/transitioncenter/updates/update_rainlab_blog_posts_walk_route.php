<?php namespace Wwrf\TransitionCenter\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateRainlabBlogWalkRoute extends Migration
{
    public function up()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->boolean('walk_route')->default(false);
        });
    }

    public function down()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->dropColumn('walk_route');
        });
    }
}
