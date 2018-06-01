<?php namespace Wwrf\TransitionCenter\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateRainlabUsersUnemployed extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->boolean('is_unemployed')->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('is_unemployed');
        });
    }
}
