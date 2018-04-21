<?php namespace Mohsin\Mobile\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AppsAddMaintenanceMessage extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('mohsin_mobile_apps', 'maintenance_message')) {
            return;
        }

        Schema::table('mohsin_mobile_apps', function($table)
        {
            $table->string('maintenance_message')->default("");
        });
    }

    public function down()
    {
        if (Schema::hasColumn('mohsin_mobile_apps', 'maintenance_message'))
        {
          Schema::table('mohsin_mobile_apps', function($table)
          {
              $table->dropColumn('maintenance_message');
          });
        }
    }

}
