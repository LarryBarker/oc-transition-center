<?php namespace Wwrf\TransitionCenter\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateTrackersTable extends Migration
{
    public function up()
    {
        Schema::create('wwrf_transitioncenter_trackers', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned()->index();
            $table->morphs('trackable');
            $table->primary(['user_id', 'trackable_id', 'trackable_type'], 'id');
            $table->integer('views');
            $table->timestamps();
            $table->timestamp('last_viewed')->nullable();
            $table->timestamp('applied_on')->nullable();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wwrf_transitioncenter_trackers');
    }
}
