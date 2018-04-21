<?php namespace Mohsin\Mobile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateAppsTable extends Migration
{

    public function up()
    {
        Schema::create('mohsin_mobile_apps', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mohsin_mobile_apps');
    }

}
