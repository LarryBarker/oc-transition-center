<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Company Model
 */
class Company extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_employment_companies';    
    
    /*
    * Validation
    */
   public $rules = [
       'name' => ['required', 'unique:wwrf_employment_companies'],
   ];

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
        'users' => ['RainLab\User\Models\User', 'key' => 'user_id', 'table' => 'wwrf_users_companies'],
        'jobs' => ['Wwrf\TransitionCenter\Models\Job']
    ];
    public $belongsTo = [
        'industry' => [
            'RainLab\Blog\Models\Category',
            'scope' => 'isIndustry'
        ]
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getJobsCountAttribute()
    {
        return $this->jobs()->has('user')->count();
    }
}
