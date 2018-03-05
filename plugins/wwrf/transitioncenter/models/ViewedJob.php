<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Model
 */
class ViewedJob extends Model
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
    public $table = 'wwrf_transitioncenter_viewed_jobs';

    protected $primaryKey = 'user_id';

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
        'job' => 'RainLab\Blog\Models\Post'
    ];

}