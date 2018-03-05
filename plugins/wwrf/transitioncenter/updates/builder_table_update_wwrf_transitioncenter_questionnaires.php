<?php namespace Wwrf\TransitionCenter\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWwrfTransitioncenterQuestionnaires extends Migration
{
    public function up()
    {
        Schema::table('wwrf_transitioncenter_questionnaires', function($table)
        {
            $table->boolean('valid_dl')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wwrf_transitioncenter_questionnaires', function($table)
        {
            $table->dropColumn('valid_dl');
        });
    }
}
