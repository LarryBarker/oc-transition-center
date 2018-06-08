<?php namespace Wwrf\TransitionCenter\ReportWidgets;

use Backend\Classes\ReportWidgetBase;

use RainLab\User\Models\User;

use Wwrf\TransitionCenter\Models\Job;

use Wwrf\TransitionCenter\Models\Employer;

use Wwrf\TransitionCenter\Models\Questionnaire;

use Wwrf\TransitionCenter\Models\AppliedJob;

use Exception;

use Request;

use DB;

use DateTime;

use DateTimeZone;

class TransitionCenter extends ReportWidgetBase
{

    public $year;

    public $quarter;

    public $month;

    public function defineProperties() {
        return [
            'title' => [
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'default'           => 'Transition Center Statistics',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ],
            'period' => [
                'title' => 'Period',
                'type' => 'dropdown',
                'options' => [
                    'yearly' => 'Yearly',
                    //'quarterly' => 'Quarterly',
                    'monthly' => 'Monthly'
                ],
                'default' => 'yearly'
            ],
            'year' => [
                'title' => 'Year',
                'default' => $this->getDefaultYear(),
                'type' => 'dropdown',
                'options' => $this->getYearOptions(),
                'placeholder' => '-- Select year --'
            ],
            //'quarter' => [
            //    'title' => 'Quarter',
            //    'type' => 'dropdown',
            //    'options' => $this->getQuarterOptions(),
            //    'depends' => ['month', 'year', 'period'],
            //    'placeholder' => '-- Select quarter --'
            //],
            'month' => [
                'title' => 'Month',
                'type' => 'dropdown',
                'options' => $this->getMonthOptions(),
                'depends' => ['year', 'quarter', 'period'],
                'placeholder' => '-- Select month --'
            ]
        ];
    }

    public function init() {

        // This will set default values on every refresh
        // we will not use $this->setProperties() as it will set
        // multiple props at single time but it will remove column size etc props as well so
        //$this->setProperty('year' , $this->getDefaultYear());
        //$this->setProperty('month' , $this->getCurrentMonth());
        //$this->setProperty('period' , 'monthly');
    }
    
    public function render() {

        try {

            $this->loadData();

            if (Request::input('year')) {
                $this->setProperty('month' , null);
            }

        }

        catch (Exception $ex) {

            $this->vars['error'] = $ex->getMessage();

        }

        return $this->makePartial('widget');

    }

    public function loadData() {

        if($this->property('year')) {
            $this->year = $this->property('year');
        }

        //if($this->property('quarter')) {
        //    $this->quarter = $this->property('quarter');
        //}

        if($this->property('month')) {
            $this->month = $this->property('month');
        }
        
        $this->loadSurveyData();

        $this->loadUserData();

        $this->loadEmploymentData();

        $this->loadPostData();

    }

    public function getYearOptions() {

        $queryAllUsers = User::all();

        $years = [];

        foreach($queryAllUsers as $user) {
            $year = date('Y', strtotime($user->created_at));

            $years[$year] = $year;
        }

        $years = array_unique($years);

        return $years;

    }

    public function getQuarterOptions() {
        
        // Load the month and year property value from POST
        $monthCode = Request::input('month');
        $yearCode = Request::input('year');
        $periodCode = Request::input('period');

        if ($periodCode == 'quarterly') {
            return [
                1 => '1st',
                2 => '2nd',
                3 => '3rd',
                4 => '4th'
            ];
        } else {
            return;
        }

        // PRIORITY is user choise if he choose something this 
        // condition will be false and we dont use default property     
        // if he dosn't choose means condition true we can use default value 
        // (so it should be page refresh)
        if (!$yearCode && !$monthCode) {

            // so if page is refreshed we use default value
            // which we set in init() method
            return [
                1 => '1st',
                2 => '2nd',
                3 => '3rd',
                4 => '4th'
            ];
        }

        // now based on $yearCode and $month code calulate -> quarter
        if ($monthCode) {
            return;
        }

        if ($yearCode) {
            return [
                1 => '1st',
                2 => '2nd',
                3 => '3rd',
                4 => '4th'
            ];
        }
    }

    public function getMonthOptions() {

        $yearCode = Request::input('year');

        $quarterCode = Request::input('quarter');

        $periodCode = Request::input('period');

        if ($periodCode == 'monthly') {
            $queryJobMonthsForYear = Job::whereYear('start_date', '=', $yearCode)->get();

            $selectedMonths = [];

            foreach($queryJobMonthsForYear as $job) {
                $month = date('m', strtotime($job->start_date));
                $monthKey = date('M', strtotime($job->start_date));

                $selectedMonths[$month] = $monthKey;
            }

            $selectedMonths = array_unique($selectedMonths);

            ksort($selectedMonths);


            return $selectedMonths;

        } else {
            return;
        }

        if ($quarterCode) {
            return;
        }

        if ($yearCode || $quarterCode) {
            $queryJobMonthsForYear = Job::whereYear('start_date', '=', $yearCode)->get();

            $selectedMonths = [];

            foreach($queryJobMonthsForYear as $job) {
                $month = date('m', strtotime($job->start_date));
                $monthKey = date('M', strtotime($job->start_date));

                $selectedMonths[$month] = $monthKey;
            }

            $selectedMonths = array_unique($selectedMonths);

            $selectedMonths = sort($selectedMonths, SORT_NUMERIC);

            return $selectedMonths;
        }

        $months = [];

        for ($m=1; $m<=12; $m++) {
            $month = date('m', mktime(0,0,0,$m, 1, date('Y')));
            $months[$month] = date('M', mktime(0,0,0,$m, 1, date('Y')));
        }

        return $months;
    }

    public function getSelectedMonths() {

        $yearCode = Request::input('year');

        $querySelectedMonthsForYear = Job::whereYear('start_date', '=', $yearCode)->get();

        $selectedMonths = [];

        foreach($querySelectedMonthsForYear as $job) {
            $month = date('m', strtotime($job->start_date));

            $selectedMonths[$month] = $month;
        }

        $selectedMonths = array_unique($selectedMonths);

        return $selectedMonths;
        
    }

    public function getDefaultYear() {

        $currentYear = date('Y');

        return $currentYear;

    }

    public function getCurrentMonth() {

        $currentMonth = date('m');

        return $currentMonth;

    }

    public function getPreviousDate() {

        $dateTimeObject = new DateTime;

        if ($this->property('year')) {
            $currentYear = $this->property('year');
            $dateTimeObject = $dateTimeObject->setDate($currentYear, 1, 1);
        }

        if ($this->property('month')) {
            $currentMonth = $this->property('month');
            $dateTimeObject = $dateTimeObject->setDate($currentYear, $currentMonth, 1);
        }

        $previousDate = $dateTimeObject->modify('-1 month');

        return $previousDate;

    }

    public function loadUserData() {
        /*
        * Load summary of current users at the facility.
        * This will change as users are removed from the facility
        * because we do not use withTrashed() on the User model.
        */

        $users = User::whereHas('groups', function($q){
            $q->where('id', '=', '2');
        })->get();

        $totalUsersForPeriod = User::whereHas('groups', function($q){
            $q->where('id', '=', '2');
        })->withTrashed()->whereYear('arrival_date','=',$this->year);

        if($this->property('period') == 'monthly' && $this->month) {
            $totalUsersForPeriod = $totalUsersForPeriod->whereMonth('arrival_date', '=', $this->month);
        }

        $totalUsersForPeriod = $totalUsersForPeriod->get();

        $this->vars['totalUsersForPeriod'] = $totalUsersForPeriod->count();

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
                                    })->isUnemployed()//->has('jobs', '<', 1)
                                      //->whereDate('eligible_date', '<', date('Y-m-d').' 00:00:00')
                                      //->orWhere('is_unemployed', 1)
                                      ->get()
                                      ->count();
    }

    public function loadSurveyData() {
        $year = $this->year;

        $query = User::whereHas('groups', function($q){
            $q->where('id', '=', '2');
        })->get();

        $questionnaires = Questionnaire::all();

        $questionnaires = Questionnaire::whereYear('updated_at', '=', $year)->whereRaw('created_at <> updated_at');

        //$questionnaires = $questionnaires->whereYear('updated_at', '=', $year)->get();

        /*$questionnaires = Questionnaire::whereHas('user', function($q){
            $q->whereHas('groups', function($subQ){
                $subQ->where('id', '=', '2');
            })->withTrashed();
        })->whereYear('created_at', '=', $year);*/

        /*$test = User::whereHas('groups', function($q){
            $q->where('id', '=', '2');
        })->whereHas('questionnaire', function($q){
            $q->where('computer_skill', '=', 'advanced');
        })->withTrashed()->get();

        dump($test->count());*/

        $this->vars['total_questionnaires'] = $total = $questionnaires->count();

        /*
         * Load data for summary of user computer skill
         */
        //$this->vars['computer_skill_beg'] = $questionnaires->where('computer_skill', '=', 'beginner')->count();
        
        $this->vars['computer_skill_beg'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('computer_skill', '=', 'beginner')->count();

        $this->vars['computer_skill_int'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('computer_skill', '=', 'intermediate')->count();

        $this->vars['computer_skill_adv'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('computer_skill', '=', 'advanced')->count();        
        
        /*
         * Load data for felony types
         */
        $this->vars['felony_person'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('felony_type', '=', 'person')->count();
        $this->vars['felony_nonperson'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('felony_type', '=', 'nonperson')->count();
        $this->vars['felony_both'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('felony_type', '=', 'both')->count();

        /*
         * Load data for resumes
        */
        $this->vars['has_resume'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('has_resume', 1)->count();

        $this->vars['no_resume'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('has_resume', 0)->count();

        $this->vars['workready'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('workready_cert', '<>', 'none')->count() / $total;

        $this->vars['onet'] = Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('interest_profiler', 1)->count();
    }

    public function loadEmploymentData() {
       
        $currentYear = $this->year;

        $currentMonth = $this->month;

        $previousYear = $this->getPreviousDate()->format('m');

        $previousMonth = $this->getPreviousDate()->format('Y');

        $startingWages = [];

        $previousStartingWages = [];

        $prevDateDiffArr = [];

        $dateDiff = [];

        $dates = [
            "1-5" => 0,
            "6-10" => 0,
            "11-20" => 0,
            "21-30" => 0,
            "30+" => 0
        ];

        /*
        *   We need to pull all users in the database who have a job.
        *   Next, we need to see if the job has a start date that matches
        *   the month/year set in the report widget properties.
        */

        $queryUsersWithJob = User::withTrashed() // all users 
                                   ->has('jobs', '>', 0) // users with AT LEAST 1 job
                                   ->whereHas('jobs', function($queryDate){
                                        $queryDate->whereYear('start_date','=', $this->year); // start date in year
                                        if($this->property('month')){  
                                            $queryDate = $queryDate->whereMonth('start_date', '=', $this->month); // start date in month
                                        }
                                    });

        $usersWithJob = $queryUsersWithJob->get();

        // run a query for the previous period
        $queryUsersWithJobsForPreviousPeriod = User::withTrashed()->has('jobs', '>', 0)->whereHas('jobs', function($q) {

            $prevMonth = $this->getPreviousDate()->format('m');

            $prevYear = $this->getPreviousDate()->format('Y');

            $this->vars['previousDate'] = $prevMonth.'-'.$prevYear;

            //$previousYear = $this->property('year') - 1;
       
            $q->whereYear('start_date','=', $prevYear);

            if($this->property('month')){

                $monthDateObj   = DateTime::createFromFormat('!m', $this->property('month'));
                $previousMonth = $monthDateObj->modify('-1 month');
                $previousMonth = $previousMonth->format('m');
                $this->vars['previousMonth'] = $previousMonth;
    
                $q = $q->whereMonth('start_date', '=', $prevMonth);

            }
            $this->vars['previousYear'] = $prevYear;
            
        });

        $getPreviousQuery = $queryUsersWithJobsForPreviousPeriod->get();
        
        /* 
        *   Check for reporting period and loop through collection
        *   Only count the job if it is the FIRST job and the start date matches month/year
        */

        if ($this->property('period') == 'yearly') { // yearly reporting period

            // users with job in current yearly period
            foreach($usersWithJob as $userWithJob) {

                $firstJob = $userWithJob->jobs->sortBy('start_date')->first(); // oldest job is first job
    
                $firstJobStartDate = strtotime($firstJob->start_date); // make date object with start date
    
                if(date('Y', $firstJobStartDate) == $this->year) { // does the year match property
                    $wage = $firstJob->start_wage;
    
                    $startingWages[$userWithJob->id] = $wage;

                    $user_id = $userWithJob->id;

                    $start_date = date_create($userWithJob->jobs->sortBy('start_date')->first()->start_date);
        
                    $eligible_date = date_create($userWithJob->eligible_date);
        
                    $datedifference = date_diff($eligible_date, $start_date);
        
                    $dateDiff[$user_id] = $datedifference->format('%a');
                }
            }

            // users with job in previous yearly period
            foreach($getPreviousQuery as $previousJob) {

                $previousJob = $previousJob->jobs->sortBy('start_date')->first();

                $previousJobStartDate = strtotime($previousJob->start_date);

                if(date('Y', $previousJobStartDate) == $previousYear) {
                    $previousJobStartWage = $previousJob->jobs->sortBy('start_date')->first()->start_wage;
    
                    $previousStartingWages[$previousJob->id] = $previousJobStartWage;
        
                    $user_id = $previousJob->id;
        
                    $prevStartDate = date_create($previousJob->jobs->sortBy('start_date')->first()->start_date);
        
                    $prevEligibleDate = date_create($previousJob->eligible_date);
        
                    $prevDateDiff = date_diff($prevEligibleDate, $prevStartDate);
        
                    $prevDateDiffArr[$user_id] = $prevDateDiff->format('%a');
                }
    
            } 

        } elseif ($this->property('period') == 'monthly') { // monthly reporting preiod

            // users with job in current monthly period
            foreach($usersWithJob as $userWithJob) {

                $firstJob = $userWithJob->jobs->sortBy('start_date')->first();
    
                $firstJobStartDate = strtotime($firstJob->start_date);
    
                if(date('Y', $firstJobStartDate) == $this->year && 
                   date('m', $firstJobStartDate) == $this->month) {
                    $wage = $firstJob->start_wage;
    
                    $startingWages[$userWithJob->id] = $wage;

                    $user_id = $userWithJob->id;

                    $start_date = date_create($userWithJob->jobs->sortBy('start_date')->first()->start_date);
        
                    $eligible_date = date_create($userWithJob->eligible_date);
        
                    $datedifference = date_diff($eligible_date, $start_date);
        
                    $dateDiff[$user_id] = $datedifference->format('%a');
                }
            }

            // users with job in current monthly period
            foreach($getPreviousQuery as $previousJob) {

                $previousJob = $previousJob->jobs->sortBy('start_date')->first();

                $previousJobStartDate = strtotime($previousJob->start_date);

                if(date('Y', $previousJobStartDate) == $previousYear && date('m', $previousJobStartDate == $previousMonth)) {
                    $previousJobStartWage = $previousJob->jobs->sortBy('start_date')->first()->start_wage;
    
                    $previousStartingWages[$previousJob->id] = $previousJobStartWage;
        
                    $user_id = $previousJob->id;
        
                    $prevStartDate = date_create($previousJob->jobs->sortBy('start_date')->first()->start_date);
        
                    $prevEligibleDate = date_create($previousJob->eligible_date);
        
                    $prevDateDiff = date_diff($prevEligibleDate, $prevStartDate);
        
                    $prevDateDiffArr[$user_id] = $prevDateDiff->format('%a');
                }
    
            } 

        }

        // Sum all starting wages and divide by total and round the average
        if (count($usersWithJob) > 0) {
            $this->vars['startingWage'] = round(array_sum($startingWages) / count($startingWages), 2);
        } else {
            $this->vars['startingWage'] = "No Users";
        }
   

        // Sum all previous starting wages and divide by total and round the average
        if (count($getPreviousQuery) > 0) {

            $this->vars['previousStartWage'] = round(array_sum($previousStartingWages) / count($getPreviousQuery), 2);

            $startingWageDiff = $this->vars['startingWage'] - $this->vars['previousStartWage'];
    
            $this->vars['startingWageDiff'] = round($startingWageDiff, 2);

        } else {
            $this->vars['startingWageDiff'] = "N/A";
        }


        // Here we are creating an array to hold the values for differences
        // between the eligible date and start date.


        // Now we loop through our users collection
        foreach ($usersWithJob as $userWithJob) {

        }

        foreach ($dateDiff as $date) {
            if ($date <= 5) {
                $dates["1-5"] += 1;
            } elseif ($date > 5 && $date <= 10) {
                $dates["6-10"] += 1;
            } elseif ($date > 10 && $date <= 20) {
                $dates["11-20"] += 1;
            } elseif ($date > 20 && $date <= 30) {
                $dates["21-30"] += 1;
            } elseif ($date > 30) {
                $dates["30+"] += 1;
            }
        }

        if (count($usersWithJob) > 0) {
            $this->vars['dateAvg'] = round(array_sum($dateDiff) / count($usersWithJob), 0);
        } else {
            $this->vars['dateAvg'] = "No Users";
        }

        if (count($getPreviousQuery) > 0) {
            $this->vars['prevDateAvg'] = round(array_sum($prevDateDiffArr) / count($getPreviousQuery), 0);

            $this->vars['prevDateAvgDiff'] = $this->vars['prevDateAvg'] - $this->vars['dateAvg'];
        } else {
            $this->vars['prevDateAvgDiff'] = "NO DATA AVAILABLE";
        }

        $this->vars['dateRange'] = $dates;
    }

    public function loadPostData() {
        $appliedJobs = AppliedJob::groupBy('job_id')
                                   ->select('job_id', DB::raw('count(*) as total'))
                                   ->orderBy('total', 'desc')
                                   ->get()
                                   ->take(10);

        $this->vars['appliedJobs'] = $appliedJobs;
    }

}