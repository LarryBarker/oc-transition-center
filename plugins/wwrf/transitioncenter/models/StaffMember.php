<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * StaffMember Model
 */
class StaffMember extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_transitioncenter_staff_members';

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
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
