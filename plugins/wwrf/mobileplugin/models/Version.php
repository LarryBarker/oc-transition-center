<?php namespace Wwrf\MobilePlugin\Models;

use Model;

/**
 * Version Model
 */
class Version extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_mobile_versions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

}
