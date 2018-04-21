<?php namespace Mohsin\Mobile\Models;

use Model;

/**
 * Platform Model
 */
class Platform extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mohsin_mobile_platforms';

    /**
     * @var boolean Turn off timestamps on this model
     */
    public $timestamps = false;

    public $rules = [
        'name' => 'required|regex:/^[\pL\d\s]+$/u'
    ];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name'];

     /**
      * @var array Relations
      */
     public $hasMany = [
         'variants' => ['Mohsin\Mobile\Models\Variant', 'delete' => 'true']
     ];

    /**
     * @var array Reserved platform names.
     */
     protected static $reserved = [
       // 'android' => 'Mohsin.Android'
     ];

    public static function isReserved($str)
    {
      return array_key_exists($str, self::$reserved);
    }

   public static function getReservedPluginName($slug)
   {
      if(array_key_exists($slug, self::$reserved))
        return self::$reserved[$slug];
   }

}
