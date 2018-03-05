<?php namespace Wwrf\TransitionCenter\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateStaffMembersTable extends Migration
{
    public function up()
    {
        Schema::create('wwrf_transitioncenter_staff_members', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wwrf_transitioncenter_staff_members');
    }
}
