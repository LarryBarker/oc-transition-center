<?php namespace Wwrf\MobilePlugin\Models;

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
    public $table = 'wwrf_mobile_variants';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    public $rules = [
        'package' => 'required|regex:/^[a-z0-9]*(\.[a-z0-9]+)+[0-9a-z]$/', // iOS and Android compliant
        'platform_id' => 'required|exists:wwrf_mobile_platforms,id',
        'description' => 'required|string|unique:wwrf_mobile_variants'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'app' => ['Wwrf\MobilePlugin\Models\App'],
        'platform' => ['Wwrf\MobilePlugin\Models\Platform']
    ];

    public $hasMany = [
        'installs' => ['Wwrf\MobilePlugin\Models\Install']
    ];

}
