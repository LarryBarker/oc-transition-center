<?php namespace Wwrf\TransitionCenter\Components;

use Cms\Classes\ComponentBase;
use Auth;
use Flash;
use Cms\Classes\Page;
use Illuminate\Support\Facades\Redirect;
use RainLab\Blog\Models\Post as Job;
use Wwrf\TransitionCenter\Models\ViewedJob;
use Wwrf\TransitionCenter\Models\AppliedJob;

class JobTracker extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'JobTracker Component',
            'description' => 'Tracks users viewed and applied jobs.'
        ];
    }

    public function defineProperties()
    {
        return ['jobPage' => [
            'title'       => 'Job Page',
            'description' => 'The page to redirect to for job post.',
            'type'        => 'dropdown',
            'default'     => 'blog/post'
        ]];
    }

    /**
     * Used for properties dropdown menu
     * @return mixed
     */
    public function getJobPageOptions()
    {
        return [''=>'- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * AJAX handler to view job
     * @return mixed
     */
    public function onViewJob($property = null, $job_id = null, $user_id = null)
    {
        $user = Auth::getUser();

        if(!isset($user_id))
            $user_id = $user->id;

        if(!isset($job_id))
            $job_id = post('job_id');

        if($job_id && $user_id != null)
        {
            $viewedJob = new ViewedJob;
            $viewedJob->user_id = $user_id;
            $viewedJob->job_id = $job_id;
            $viewedJob->save();

            $job = Job::where('id', '=', $job_id)->first();

            $url = $property == null ? $this->property('jobPage') . "/" . $job->slug : $property . "/" . $job->slug;

            return Redirect::intended($url);
        }
    }

    /**
     * AJAX handler to apply job
     * @return mixed
     */
    public function onApplyJob($job_id = null, $user_id = null)
    {
        $user = Auth::getUser();

        if(!isset($user_id))
            $user_id = $user->id;

        if(!isset($job_id))
            $job_id = post('job_id');

        $userAppliedJob = AppliedJob::where('job_id','=',$job_id)
                                    ->where('user_id','=',$user_id)
                                    ->first();

        if($userAppliedJob) {
            Flash::error('You already applied to this job!');

            return $jobLink = $userAppliedJob->job->link;
        }

        if(!$userAppliedJob) {
            $appliedJob = new AppliedJob;
            $appliedJob->user_id = $user_id;
            $appliedJob->job_id = $job_id;
            $appliedJob->save();

            $job = Job::where('id', '=', $job_id)->first();

            $url = $job->link;
            
            //return Redirect::intended($url);
        }
    }

    public function onFavoriteJob($job_id = null) {

        $user = Auth::getUser();

        if(!isset($job_id))
            $job_id = post('job_id');
        
        $job = Job::where('id', '=', $job_id)->first();

        $job->addFavorite($user);

        return;
    }

    public function onToggleFavorite($job_id = null) {

        $user = Auth::getUser();

        if(!isset($job_id))
            $job_id = post('job_id');
        
        $job = Job::where('id', '=', $job_id)->first();

        $job->toggleFavorite($user);

        return;
    }
}
