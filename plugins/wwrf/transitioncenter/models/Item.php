<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Model
 */
class Item extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_resources_items';

    public $belongsTo = [
        'category' => [
            'Wwrf\TransitionCenter\Models\Category',
            'table' => 'wwrf_resources_items_categories',
            'order' => 'name'
        ]
    ];

    public $attachMany = [
        'images' => ['System\Models\File'],
        'files' => ['System\Models\File']
    ];
}