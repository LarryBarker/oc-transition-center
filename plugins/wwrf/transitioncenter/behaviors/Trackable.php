<?php namespace Wwrf\TransitionCenter\Behaviors;

use Wwrf\TransitionCenter\Models\Tracker;
use Auth;
use October\Rain\Database\Collection;
use October\Rain\Extension\ExtensionBase;
use RainLab\User\Models\User;


class Trackable extends ExtensionBase
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

        $this->model->morphMany['trackers'] = [
            'Wwrf\TransitionCenter\Models\Tracker',
            'name' => 'trackable'
        ];
    }

    /**
     * Add this Object to the user trackers
     *
     * @param   User|null   $user   (if null it is added to the authenticated user)
     */
    public function addTracker(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();

        // Make sure the object isn't tracked already, as it leads to database errors
        if(! $this->model->isTracked($user)) {
            $tracker = new Tracker([
                'user_id' => ($user->id) ? $user->id : Auth::getUser()->id,
                'last_viewed' => $this->model->freshTimestamp()
                ]);
            $this->model->trackers()->save($tracker);
        }
    }

    /**
     * Add a favorite tracker for the Object to the user
     * 
     * @param User|null $user
     */
    public function addFavoriteTracker(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();

        if(!$this->model->isTracked($user)){
            $tracker = new Tracker([
                'user_id' => ($user->id) ? $user->id : Auth::getUser()->id,
                'is_favorite' => 1
            ]);
            $this->model->trackers()->save($tracker);
        }

        if($this->model->isTracked($user)){
            $this->timestamps = false;

            $this
                ->model
                ->trackers()
                ->where('user_id', $user->id)
                ->update(['is_favorite' => 1]);
        }
    }

    /**
     * Check if the user has favorited this Object
     *
     * @param   User|null   $user   (if null it is added to the authenticated user)
     * @return boolean
     */
    public function isTrackerFavorited(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();
        return $this->model->trackers()->where('user_id', $user->id)->where('is_favorite', 1)->exists();
    }

    /**
     * Remove this Object from the user trackers
     *
     * @param   User|null   $user   (if null it is added to the authenticated user)
     *
     */
    public function removeTracker(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();
        $this->model->trackers()->where('user_id', $user->id)->delete();
    }

    /**
     * Toggle the tracker status from this Object
     *
     * @param   User|null   $user   (if null it is added to the authenticated user)
     */
    public function toggleTracker(User $user = null)
    {
        $this->model->isTracked($user) ? $this->model->removeTracker($user) : $this->model->addTracker($user);
    }

    /**
     * Check if the user has tracked this Object
     *
     * @param   User|null   $user   (if null it is added to the authenticated user)
     * @return boolean
     */
    public function isTracked(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();
        return $this->model->trackers()->where('user_id', $user->id)->exists();
    }

    /**
     * Return a collection with the Users who tracked this Object.
     *
     * @return Collection
     */
    public function trackedBy()
    {
        return $this->model->trackers()->with('user')->get()->mapWithKeys(function ($item) {
            return [$item['user']];
        });
    }

    /**
     * Count the number of trackers
     *
     * @return int
     */
    public function getTrackersCountAttribute()
    {
        return $this->model->trackers()->count();
    }

    /**
     * Return a collection with the Users who applied to this Job.
     * 
     * @return Collection
     */
    public function appliedBy()
    {
        return $this->model->trackers()->with('user')->where('applied_on')->get()->mapWithKeys(function ($item) {
            return [$item['user']];
        });
    }

    /**
     * Check if the user has applied to this job
     * 
     * @param User|null $user   
     * @return boolean
     */
    public function isApplied(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();
        
        return $this
                    ->model
                    ->trackers()
                    ->where('user_id', $user->id)
                    ->where('applied_on', '!=', NULL)
                    ->exists();
    }

    /**
     * Add this job to the users applied trackers
     * 
     * @param User|null $user
     */
    public function addApply(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();

        //$oldTimestamps = $this->timestamps;
        $this->timestamps = false;

        $this
            ->model
            ->trackers()
            ->where('user_id', $user->id)
            ->update(['applied_on' => $this->model->freshTimestamp()])
        ;

        //$this->applied_on = $this->model->freshTimestamp();
        //$this->timestamps = $oldTimestamps;
    }

    /**
     * Update this job tracker for user
     * 
     * @param User|null $user
     */
    public function updateViewedJob(User $user = null)
    {
        $user = $user ? $user : Auth::getUser();

        //$oldTimestamps = $this->timestamps;
        $this->timestamps = false;

        $this
            ->model
            ->trackers()
            ->where('user_id', $user->id)
            ->update(['last_viewed' => $this->model->freshTimestamp()])
        ;

        //$this->timestamps = $oldTimestamps;
    }
}