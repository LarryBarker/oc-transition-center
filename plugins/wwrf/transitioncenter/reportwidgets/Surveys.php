<?php namespace Wwrf\TransitionCenter\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use RainLab\User\Models\User;
use Wwrf\TransitionCenter\Models\Job;
use Wwrf\TransitionCenter\Models\Questionnaire;
use Exception;
use DB;

class Surveys extends ReportWidgetBase
{
    public function render()
    {
        try {
            $this->loadData();
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
                'default'           => 'wwrf.transitioncenter::lang.plugin.name',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ],
            'year' => [
                'title'             => 'Year',
                'type'              => 'dropdown',
                'options'           => $this->getYearOptions(),
            ]
        ];
    }

    /*
     * @var array
     *
     */

    public $years;

    protected function loadData()
    {
        $year = $this->property('year');

        $query = User::whereHas('groups', function($q){
            $q->where('id', '=', '2');
        })->get();

        //$questionnaires = Questionnaire::all();

        //$questionnaires = Questionnaire::whereYear('updated_at', '=', $year)->whereRaw('created_at <> updated_at')->get();

        //$questionnaires = $query->questionnaires->whereYear('updated_at', '=', $year)->get();

        $questionnaires = Questionnaire::whereHas('user', function($q){
            $q->whereHas('groups', function($subQ){
                $subQ->where('id', '=', '2');
            })->withTrashed();
        })->whereYear('created_at', '=', $year);

        /*$test = User::whereHas('groups', function($q){
            $q->where('id', '=', '2');
        })->whereHas('questionnaire', function($q){
            $q->where('computer_skill', '=', 'advanced');
        })->withTrashed()->get();

        dump($test->count());*/

        //$total = Questionnaire::whereYear('updated_at', '=', $year)->whereRaw('created_at <> updated_at')->count();

        $this->vars['total_questionnaires'] = $total = $questionnaires->count();

        /*
         * Load data for summary of user computer skill
         */
        $this->vars['computer_skill_beg'] = count($questionnaires->where('computer_skill', '=', 'beginner'));
        
        //Questionnaire::whereYear('created_at', '=', $year)->whereRaw('created_at <> updated_at')->where('computer_skill', '=', 'beginner')->count();

        $this->vars['computer_skill_int'] = $questionnaires->where('computer_skill', '=', 'intermediate')->count();

        $this->vars['computer_skill_adv'] = $questionnaires->where('computer_skill', '=', 'advanced')->count();        
        
        /*
         * Load data for summary of WORKReady certificates
         */
        $this->vars['bronze'] = $questionnaires->where('workready_cert', '=', 'bronze')->count();
        $this->vars['silver'] = $questionnaires->where('workready_cert', '=', 'silver')->count();
        $this->vars['gold'] = $questionnaires->where('workready_cert', '=', 'gold')->count();
        $this->vars['plat'] = $questionnaires->where('workready_cert', '=', 'platinum')->count();

        /*
         * Load data for felony types
         */
        $this->vars['felony_person'] = $questionnaires->where('felony_type', '=', 'person')->count();
        $this->vars['felony_nonperson'] = $questionnaires->where('felony_type', '=', 'nonperson')->count();
        $this->vars['felony_both'] = $questionnaires->where('felony_type', '=', 'both')->count();

        /*
         * Load data for resumes
        */
        $this->vars['has_resume'] = $questionnaires->where('has_resume', '=', '1')->count();
        $this->vars['no_resume'] = $questionnaires->where('has_resume', '!=', '1')->count();

        $this->vars['workready'] = $questionnaires->where('workready_cert', '!=', 'none')->count() / $total;

        $this->vars['onet'] = $questionnaires->where('interest_profiler', 1)
                                            ->count();

    }

    public function getYearOptions()
    {
        $query = Questionnaire::all();

        $years = [];

        foreach($query as $questionnaire) {
            $year = date('Y', strtotime($questionnaire->created_at));

            $years[$year] = $year;
        }

        $years = array_unique($years);

        return $years;
    }
}