<?php namespace Wwrf\TransitionCenter\Behaviors;

use October\Rain\Database\Collection;
use October\Rain\Extension\ExtensionBase;


class Trackability extends ExtensionBase
{
    /**
     * @var \October\Rain\Database\Model Reference to the extended model.
     */
    protected $model;

    /**
     * Constructor
     * @param \October\Rain\Database\Model $model The extended model.
     */
    public function __construct($model)
    {
        $this->model = $model;

        $this->model->hasMany['trackers'] = [
            'Wwrf\TransitionCenter\Models\Tracker', 
            'key' => 'user_id', 
            'order' => 'last_viewed desc'
        ];
    }

    /**
     * Return a collection with the User tracked Model.
     * The Model needs to have the Trackable trait
     *
     * @param  $class *** Accepts for example: Post::class or 'App\Post' ****
     * @return Collection
     */
    public function trackerOf($class)
    {
        return $this->model->trackers()->where('trackable_type', $class)->with('trackable')->get();
    }

    /**
     * Add the object to the User trackers.
     * The Model needs to have the Trackable behavior
     *
     * @param Object $object
     */
    public function addTracker($object)
    {
        $object->addTracker($this->model);
    }

    /**
     * Remove the Object from the user trackers.
     * The Model needs to have the Trackable behavior
     *
     * @param Object $object
     */
    public function removeTracker($object)
    {
        $object->removeTracker($this->model);
    }

    /**
     * Toggle the tracker status from this Object from the user favorites.
     * The Model needs to have the Trackable behavior
     *
     * @param Object $object
     */
    public function toggleTracker($object)
    {
        $object->toggleTracker($this->model);
    }

    /**
     * Check if the user has tracked this Object
     * The Model needs to have the Trackable behavior
     *
     * @param Object $object
     * @return boolean
     */
    public function isTracked($object)
    {
        return $object->isTracked($this->model);
    }

    /**
     * Check if the user has tracked this Object
     * The Model needs to have the Trackable behavior
     *
     * @param Object $object
     * @return boolean
     */
    public function hasTracked($object)
    {
        return $object->hasTracked($this->model);
    }

    /**
     * Add job application tracker to Object
     * The Model needs to have the Trackable behavior
     * 
     * @param Object $object
     */

    public function trackApplied($object)
    {
        return $object->trackApplied($this->model);
    }

    /**
     * Check if the user has applied to this Object
     * The model needs to have the Trackable behavior
     * 
     * @param Object $object
     * @return boolean
     */
    public function hasApplied($object)
    {
        return $object->hasApplied($this->model);
    }

}