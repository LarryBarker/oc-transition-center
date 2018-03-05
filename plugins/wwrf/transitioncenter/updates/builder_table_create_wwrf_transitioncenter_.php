<?php namespace Wwrf\TransitionCenter\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWwrfTransitioncenter extends Migration
{
    public function up()
    {
        Schema::create('wwrf_transitioncenter_', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('type_of_work')->nullable();
            $table->text('accept_felonies')->nullable();
            $table->text('rejected_felonies')->nullable();
            $table->text('employment_type')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wwrf_transitioncenter_');
    }
}
