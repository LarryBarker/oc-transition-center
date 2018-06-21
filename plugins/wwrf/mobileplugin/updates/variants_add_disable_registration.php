<?php namespace Wwrf\MobilePlugin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class VariantsAddDisableRegistration extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('wwrf_mobile_variants', 'disable_registration')) {
            return;
        }

        Schema::table('wwrf_mobile_variants', function($table)
        {
            $table->boolean('disable_registration')->default(0);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('wwrf_mobile_variants', 'disable_registration'))
        {
          Schema::table('wwrf_mobile_variants', function($table)
          {
              $table->dropColumn('disable_registration');
          });
      }
    }

}
