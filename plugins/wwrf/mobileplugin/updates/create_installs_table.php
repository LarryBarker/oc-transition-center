<?php namespace Wwrf\MobilePlugin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateInstallsTable extends Migration
{

    public function up()
    {
        Schema::create('wwrf_mobile_installs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('instance_id', 16);
            $table->integer('variant_id')->unsigned()->index();
            $table->unique(['instance_id', 'variant_id']);
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wwrf_mobile_installs');
    }

}
