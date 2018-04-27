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

    public $hasMany = [
        'usersprograms' => [
            'Wwrf\TransitionCenter\Models\UserProgram',
            'table' => 'wwrf_programtracker_users_programs'
        ],
        'attending_count' => [
            'Wwrf\TransitionCenter\Models\UserProgram',
            'table' => 'wwrf_programtracker_users_programs',
            'conditions' => 'status = "attending"',
            'count' => true
        ],
        'completed_count' => [
            'Wwrf\TransitionCenter\Models\UserProgram',
            'table' => 'wwrf_programtracker_users_programs',
            'conditions' => 'status = "completed"',
            'count' => true
        ],
        'declined_count' => [
            'Wwrf\TransitionCenter\Models\UserProgram',
            'table' => 'wwrf_programtracker_users_programs',
            'conditions' => 'status = "declined"',
            'count' => true
        ],
        'na_count' => [
            'Wwrf\TransitionCenter\Models\UserProgram',
            'table' => 'wwrf_programtracker_users_programs',
            'conditions' => 'status = "na"',
            'count' => true
        ]
    ];
}
