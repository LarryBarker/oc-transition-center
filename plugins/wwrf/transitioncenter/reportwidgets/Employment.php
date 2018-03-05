<?php namespace Wwrf\TransitionCenter\ReportWidgets;



use Backend\Classes\ReportWidgetBase;

use RainLab\User\Models\User;

use Wwrf\TransitionCenter\Models\Job;

use Exception;

use DB;

use Wwrf\TransitionCenter\Models\Employer;

class Employment extends ReportWidgetBase

{
    
    /*

     * @var array

     *

     */

    public $users;
     
    public $dates;

    public $years;

    public $months;

    public function render()

    {

        try {

            $this->loadData();

            $this->users = $this->loadData()['$users'];

        }

        catch (Exception $ex) {

            $this->vars['error'] = $ex->getMessage();

        }

        return $this->makePartial('widget');

    }



    public function defineProperties()

    {

        return [

            'title' => [

                'title'             => 'backend::lang.dashboard.widget_title_label',

                'default'           => 'Employment',

                'type'              => 'string',

                'validationPattern' => '^.+$',

                'validationMessage' => 'backend::lang.dashboard.widget_title_error'

            ],
            'year' => [
                'title' => 'Year',
                'default' => $this->getDefaultYear(),
                'type' => 'dropdown',
                'options' => $this->getYearOptions(),
            ],
            'quarter' => [
                'title' => 'Quarter',
                'type' => 'dropdown',
                'options' => [
                    1 => '1st',
                    2 => '2nd',
                    3 => '3rd',
                    4 => '4th'
                ]
            ],
            'month' => [
                'title' => 'Month',
                'type' => 'dropdown',
                'options' => $this->getMonthOptions()
            ]
        ];

    }

    protected function loadData()

    {

        /*
         * Load summary of current users at the facility.
         * This will change as users are removed from the facility
         * because we do not use withTrashed() on the User model.
         */

        $users = User::whereHas('groups', function($q){
                        $q->where('id', '=', '2');
                    })->get();

        // Users are available only if they can start work immediately.
        $this->vars['available'] = $users->where('status', 'available')->count();

        // Employed users are allowed to look for jobs
        $this->vars['employed'] = $users->where('status', 'employed')->count();

        // Unavailable users may have a job 
        // but are not allowed to look for employment
        $this->vars['unavailable'] = $users->where('status', 'unavailable') ->count();

        $this->vars['total_users'] = $users->count();

        $this->vars['unemployed'] = User::whereHas('groups', function($q){
            
                                            $q->where('id', '=', '2');
            
                                      })->has('jobs', '<', 1)
                                        ->whereDate(
                                            'eligible_date', 
                                            '<', 
                                            date('Y-m-d').' 00:00:00'
                                        )
                                        ->get()
                                        ->count();

        /*
         * We need to pull all users in the database who have a job.
         * Next, we need to see if the job has a start date that matches
         * the year set in the report widget properties.
         * 
         * We then create an array to hold all of the starting wages and
         * loop over the collection, pulling the start wage for the users
         * FIRST job and adding it to the array.
         * 
         * Finally, we calculate the average starting wage.
         */
        
        /*
         * This was the original query which looked at ALL jobs and their
         * starting wage. This is innacurate because it includes previous jobs, 
         * which we do not want to account for.
         * 
         * $wages = Job::where('start_wage', '>', 0)->avg('start_wage');
         * 
         * $users = User::withTrashed()->has('jobs', '>', 0)->get();
         */

        // Get the collection of all users using withTrashed().
        // Narrow the collecting by asking for users that have more than 1 job.
        // Next, look at the users who's jobs have a start date equal
        // to the year set in the widget property.

        $query = User::withTrashed()->has('jobs', '>', 0)->whereHas('jobs', function($q){$q->whereYear('start_date','=',$this->property('year'));});

        $users = $query->get();

        $startingWages = [];

        foreach($users as $user) {

            $wage = $user->jobs->first()->start_wage;

            $startingWages[$user->id] = $wage;
            
        }

        // Sum all starting wages and divide by total and round the average
        $this->vars['wage'] = round(array_sum($startingWages) / count($users), 2);
        
        

        /*
         * This looks at average starting time for jobs. It is similar to 
         * average starting wage because we need to look for users with jobs 
         * and jobs that started in the specified year.
         */

        /*
         * We no longer need this query. We will reuse the $query 
         * and apply a where condition for only users that have 
         * an eligible date. Otherwise we will get an error.
         * 
         * $usersJobs = User::has('jobs', '>', 0)->whereHas('jobs', function($q
         * {
         * $q->where('start_date', '>', 0);
         * })->where('eligible_date', '>', 0)->get();
         * 
         * $jobs = User::has('jobs', '>', 0)->get();
         */

        // Here are are creating an array to hold the values for differences
        // between the eligible date and start date.
        $dateDiff = [];
        
        // Now we loop through our users collection
        foreach ($users as $user) {

            $user_id = $user->id;

            $start_date = date_create($user->jobs->first()->start_date);

            $eligible_date = date_create($user->eligible_date);

            $datedifference = date_diff($eligible_date, $start_date);

            $dateDiff[$user_id] = $datedifference->format('%a');

        }

        $this->vars['dateAvg'] = round(array_sum($dateDiff) / count($users), 0);

        // Pull most recent employer registration to display on dashboard
        $newEmployer = User::whereHas('groups', function($q){

                        // User group id = 3 is employers group
                        $q->where('id', '=', '3');

                    // Sort newest to oldest registration date
                    // Take the first record
                    })->orderBy('created_at', 'desc')->take(1)->get();

        $this->vars['newEmployer'] = $newEmployer->first();

    }

    public function getYearOptions() {

        $query = User::all();

        $years = [];

        foreach($query as $user) {
            $year = date('Y', strtotime($user->created_at));

            $years[$year] = $year;
        }

        $years = array_unique($years);

        return $years;

    }

    public function getMonthOptions() {

        /*if($this->property('year')){
            $query = User::all();

            $months = [];

            foreach($query as $user) {
                $month = date('m', strtotime($user->created_at));

                $months[$month] = date('M', strtotime($user->created_at));
            }

            $months = array_unique($months);

            return $months;
        }*/

        $months = [];

        for ($m=1; $m<=12; $m++) {

            $month = date('m', mktime(0,0,0,$m, 1, date('Y')));

            $months[$month] = date('M', mktime(0,0,0,$m, 1, date('Y')));

        }

        return $months;
    }

    public function getDefaultYear() {

        $currentYear = date('Y');

        return [$currentYear => $currentYear];

    }

}