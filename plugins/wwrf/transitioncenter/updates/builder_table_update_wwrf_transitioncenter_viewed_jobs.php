<?php namespace Wwrf\TransitionCenter\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWwrfTransitioncenterViewedJobs extends Migration
{
    public function up()
    {
        Schema::table('wwrf_transitioncenter_viewed_jobs', function($table)
        {
            $table->dropPrimary(['user_id','job_id']);
            $table->integer('user_id')->nullable()->change();
            $table->integer('job_id')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('wwrf_transitioncenter_viewed_jobs', function($table)
        {
            $table->integer('user_id')->nullable(false)->change();
            $table->integer('job_id')->nullable(false)->change();
            $table->primary(['user_id','job_id']);
        });
    }
}
