<?php namespace Wwrf\MobilePlugin\Models;

use Model;

/**
 * Install Model
 */
class Install extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'instance_id'   => 'required|max:16|unique_with:wwrf_mobile_installs,variant_id',
        'variant_id'    => 'required|integer|exists:wwrf_mobile_variants,id'
    ];

    public $throwOnValidation = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_mobile_installs';

    public $implement = ['Wwrf.MobilePlugin.Classes.VariantModel'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Datetime fields
     */
     protected $dates = [
         'last_seen',
         'created_at',
         'updated_at'
     ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'variant' => ['Wwrf\MobilePlugin\Models\Variant']
    ];

    //
    // Last Seen
    //

    /**
     * Checks if the user has been seen in the last 5 minutes, and if not,
     * updates the last_seen timestamp to reflect their online status.
     * @return void
     */
    public function touchLastSeen()
    {
        if ($this->isOnline()) {
            return;
        }

        $oldTimestamps = $this->timestamps;
        $this->timestamps = false;

        $this
            ->newQuery()
            ->where('id', $this->id)
            ->update(['last_seen' => $this->freshTimestamp()])
        ;

        $this->timestamps = $oldTimestamps;
    }

    /**
     * Returns true if the user has been active within the last 5 minutes.
     * @return bool
     */
    public function isOnline()
    {
        return $this->getLastSeen() > $this->freshTimestamp()->subMinutes(5);
    }

    /**
     * Returns the date this user was last seen.
     * @return Carbon\Carbon
     */
    public function getLastSeen()
    {
        return $this->last_seen ?: $this->created_at;
    }
}
