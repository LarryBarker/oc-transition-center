<?php namespace Wwrf\TransitionCenter\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWwrfTransitioncenterAppliedJobs extends Migration
{
    public function up()
    {
        Schema::create('wwrf_transitioncenter_applied_jobs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('user_id');
            $table->integer('job_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary(['user_id','job_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wwrf_transitioncenter_applied_jobs');
    }
}
