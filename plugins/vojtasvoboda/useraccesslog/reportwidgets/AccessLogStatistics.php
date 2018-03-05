<?php namespace VojtaSvoboda\UserAccessLog\ReportWidgets;

use App;
use ApplicationException;
use Exception;
use Backend\Classes\ReportWidgetBase;
use VojtaSvoboda\UserAccessLog\Models\AccessLog;
use Carbon\Carbon;
use RainLab\User\Models\UserGroup;
use RainLab\User\Models\User;
use DB;

/**
 * AccessLogStatistics overview widget.
 *
 * @package \VojtaSvoboda\UserAccessLog\ReportWidgets
 */
class AccessLogStatistics extends ReportWidgetBase
{
    /**
     * Renders the widget.
     */
    public function render()
    {
        try {
            $this->vars['all'] = $this->getCounts()['all'];
            $this->vars['counts'] = $this->getCounts()['counts'];
            //$this->vars['sql'] = $this->getCounts()['sql'];

        } catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
            $this->vars['all'] = 0;
            $this->vars['counts'] = [];
        }

        return $this->makePartial('widget');
    }

    /**
     * Define widget properties
     *
     * @return array
     */
    public function defineProperties()
    {
        return [
            'title' => [
                'title' => 'vojtasvoboda.useraccesslog::lang.reportwidgets.statistics.title',
                'default' => 'Access statistics',
                'type' => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'vojtasvoboda.useraccesslog::lang.reportwidgets.statistics.title_validation'
            ],
            'days' => [
                'title' => 'vojtasvoboda.useraccesslog::lang.reportwidgets.chartlineaggregated.days_title',
                'default' => '30',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
            ],
            'group' => [
                'title' => 'Group',
                'description' => 'Select group to report...',
                'type' => 'dropdown',
                'options' => $this->getGroupOptions(),
                'default' => 'Users',
			],
        ];
    }

    /**
     * Get data for widget
     *
     * @return array
     */
    public function getCounts()
    {
        //$log = AccessLog::all()->groupBy('user_id');

        $days = $this->property('days');

        $group = $this->property('group');

        if (!$days) {
            throw new ApplicationException('Invalid days value: ' . $days);
        }

        //$log = AccessLog::all()->groupBy('user_id');

        /*$sql = DB::table('user_access_log')
        ->select(DB::raw('*, count(user_id) as user_count'))
        ->groupBy('user_id')
        ->orderBy('user_count', 'DESC');

        $sql = $sql->get();*/

        //$log = AccessLog::has('user')->get()->groupBy('user_id');

        $log = AccessLog::whereHas('user', function($q)
        {
            $q->whereHas('groups', function($subquery)
                {
                    $subquery->where('name', $this->property('group'));
                });
        })->where('created_at', '>=', Carbon::now()->subDays($days)->format('Y-m-d'))->get()->groupBy('user_id');
          
        $counts = [];

        $all = 0;

        foreach ($log as $l) {
            $first = $l[0];
            //$user = $first->user ? $first->user : $this->getDeletedFakeUser();
            $user = $first->user;
            $size = sizeof($l);
            $counts[] = [
                'size' => $size,
                'id' => $first->user_id,
                'name' => $user->name.' '.$user->surname,
                'company' => $user->company_name,
                'number' => $user->kdoc_number,
                'group' => $user->groups->first()->name
            ];
            $all += $size;
        }

        //dump($sql);

        return [
            'all' => $all,
            'counts' => $counts,
            'log' => $log,
        ];

    }

    /**
     * Get fake User object for deleted users
     *
     * @return \stdClass
     */
    public function getDeletedFakeUser()
    {
        $user = new \stdClass();
        $user->username = 'Deleted users';
        $user->name = 'Deleted';
        $user->surname = 'User';

        return $user;
    }

    public function getGroupOptions() {
		return UserGroup::orderBy('name')->lists('name', 'name');
	}
}
