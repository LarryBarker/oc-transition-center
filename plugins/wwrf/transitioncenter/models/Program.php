<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Model
 */
class Program extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_programtracker_programs';

    public $belongsTo = [
        'userprogram' => [
            'Wwrf\TransitionCenter\Models\UserProgram',
        ]
    ];
}
