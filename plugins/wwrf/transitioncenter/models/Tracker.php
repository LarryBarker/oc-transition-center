<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Tracker Model
 */
class Tracker extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_transitioncenter_trackers';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'user_id',
        'last_viewed',
        'is_favorite'
    ];

    protected $dates = [
        'last_viewed',
        'applied_on',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'user' => 'RainLab\User\Models\User'
    ];
    public $belongsToMany = [];
    public $morphTo = [
        'trackable' => []
    ];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
