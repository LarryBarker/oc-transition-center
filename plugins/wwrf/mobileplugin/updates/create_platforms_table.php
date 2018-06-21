<?php namespace Wwrf\MobilePlugin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreatePlatformsTable extends Migration
{

    public function up()
    {
        Schema::create('wwrf_mobile_platforms', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wwrf_mobile_platforms');
    }

}
