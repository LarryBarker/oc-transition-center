<?php namespace Mohsin\Mobile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreatePlatformsTable extends Migration
{

    public function up()
    {
        Schema::create('mohsin_mobile_platforms', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mohsin_mobile_platforms');
    }

}
