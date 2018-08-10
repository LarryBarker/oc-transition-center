<?php namespace Wwrf\TransitionCenter\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateRainlabBlogApplyOptions extends Migration
{
    public function up()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->boolean('apply_online')->default(false);
            $table->boolean('email_resume')->default(false);
            $table->boolean('in_person')->default(false);
        });
    }

    public function down()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->dropColumn('apply_online');
            $table->dropColumn('email_resume');
            $table->dropColumn('in_person');
        });
    }
}
