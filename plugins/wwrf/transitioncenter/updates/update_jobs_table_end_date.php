<?php namespace Wwrf\TransitionCenter\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateJobsTableEndDate extends Migration
{
    public function up()
    {
        Schema::table('wwrf_users_jobs', function($table)
        {
            $table->date('end_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('wwrf_users_jobs', function($table)
        {
            $table->dropColumn('end_date');
        });
    }
}
