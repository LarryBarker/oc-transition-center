<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Model
 */
class AppliedJob extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_transitioncenter_applied_jobs';

    public $belongsTo =[
        'user' => 'RainLab\User\Models\User',
        'job' => 'RainLab\Blog\Models\Post'
    ];
}