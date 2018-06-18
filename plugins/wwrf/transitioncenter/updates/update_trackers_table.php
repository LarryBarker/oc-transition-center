<?php namespace Wwrf\TransitionCenter\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateTrackersTable extends Migration
{
    public function up()
    {
        Schema::table('wwrf_transitioncenter_trackers', function($table)
        {
            $table->boolean('is_favorite')->default(false);
        });
    }

    public function down()
    {
        Schema::table('wwrf_transitioncenter_trackers', function($table)
        {
            $table->dropColumn('is_favorite');
        });
    }
}
