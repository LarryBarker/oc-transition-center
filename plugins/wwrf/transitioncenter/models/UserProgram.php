<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Model
 */
class UserProgram extends Model
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
    public $table = 'wwrf_programtracker_users_programs';
    
    public $belongsTo = [
        'program' => [
            'Wwrf\TransitionCenter\Models\Program',
            'key' => 'program_id'
        ],
        'user' => [
            'RainLab\User\Models\User',
            'key' => 'user_id'
        ]
    ];
}
