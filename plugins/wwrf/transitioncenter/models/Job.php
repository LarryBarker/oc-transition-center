<?php namespace Wwrf\TransitionCenter\Models;

use Model;
use RainLab\User\Models\User;

/**
 * Job Model
 */
class Job extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_users_jobs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [
    ];
    public $hasMany = [
    ];
    public $belongsTo = [
        'company' => ['Wwrf\TransitionCenter\Models\Company', 'order' => 'name asc'],
        'user' => ['RainLab\User\Models\User']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Scope a query to only filter according to date.
     */
    public function scopeFilterDates($query, $afterDate, $beforeDate)
    {
        return $query->where('start_date', '>=', $afterDate)->where('start_date', '<=', $beforeDate);
    }

    public function scopeIsCurrentJob($query)
    {
        return $query->has('user');
    }
}
