<?php namespace Mohsin\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class VariantsAddDisableRegistration extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('mohsin_mobile_variants', 'disable_registration')) {
            return;
        }

        Schema::table('mohsin_mobile_variants', function($table)
        {
            $table->boolean('disable_registration')->default(0);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('mohsin_mobile_variants', 'disable_registration'))
        {
          Schema::table('mohsin_mobile_variants', function($table)
          {
              $table->dropColumn('disable_registration');
          });
      }
    }

}
