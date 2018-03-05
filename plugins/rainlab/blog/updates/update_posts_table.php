<?php namespace RainLab\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdatePostsTable extends Migration
{

    public function up()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('link')->nullable();
            $table->string('website')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->dropColumn('company');
            $table->dropColumn('address');
            $table->dropColumn('phone');
            $table->dropColumn('link');
            $table->dropColumn('website');
        });
    }

}
