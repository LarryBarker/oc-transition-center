<?php namespace Mohsin\Mobile\Models;

use Model;

/**
 * Variant Model
 */
class Variant extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mohsin_mobile_variants';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    public $rules = [
        'package' => 'required|regex:/^[a-z0-9]*(\.[a-z0-9]+)+[0-9a-z]$/', // iOS and Android compliant
        'platform_id' => 'required|exists:mohsin_mobile_platforms,id',
        'description' => 'required|string|unique:mohsin_mobile_variants'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'app' => ['Mohsin\Mobile\Models\App'],
        'platform' => ['Mohsin\Mobile\Models\Platform']
    ];

    public $hasMany = [
        'installs' => ['Mohsin\Mobile\Models\Install']
    ];

}
