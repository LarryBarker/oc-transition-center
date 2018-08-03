<?php namespace Wwrf\TransitionCenter\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateTempServicesTable extends Migration
{

    public function up()
    {
        Schema::table('wwrf_transitioncenter_', function($table)
        {
            $table->string('slug')->index();
        });
    }

    public function down()
    {
        Schema::table('wwrf_transitioncenter_', function($table)
        {
            $table->dropColumn('slug');
        });
    }

}
