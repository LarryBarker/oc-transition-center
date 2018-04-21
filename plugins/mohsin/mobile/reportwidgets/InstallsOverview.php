<?php namespace Mohsin\Mobile\ReportWidgets;

use Exception;
use Carbon\Carbon;
use ApplicationException;
use Mohsin\Mobile\Models\Install;
use Mohsin\Mobile\Models\Variant;
use Backend\Classes\ReportWidgetBase;

 /**
 * App Installs overview widget.
 *
 * @package backend
 * @author Saifur Rahman Mohsin
 */
class InstallsOverview extends ReportWidgetBase
{
    /**
     * Renders the widget.
     */
    public function render()
    {
        $this->vars['app_name'] = Variant::find($this->property('variant'))->description;

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
                'default'           => e(trans('mohsin.mobile::lang.widgets.title_installs')),
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ],
            'days' => [
                'title'             => 'Number of days to display data for',
                'default'           => '30',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$'
            ],
            'variant' => [
                'title'             => 'The app to display the data for',
                'default'           => '1',
                'type'              => 'dropdown',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'A valid app must be selected.'
            ]
        ];
    }

    public function getVariantOptions()
    {
        return Variant::lists('description', 'id');
    }

    protected function loadData()
    {
        $days = $this->property('days');
        $variant = $this->property('variant');
        if (!$days)
            throw new ApplicationException('Invalid days value: '.$days);

        $installs = Install::select('created_at')
          ->orderBy('created_at', 'desc')
          ->where('created_at', '>=', Carbon::now()->subDays($days))
          ->where('variant_id', '=', $variant)
          ->get()
          ->groupBy(function($date) {
              return Carbon::parse($date->created_at)->format('d M'); // grouping by years
          });

        $points = [];
        foreach ($installs as $key => $value)
        {
            $point = [
                strtotime("+1 day", strtotime($key)) * 1000,
                count($value)
            ];
            $points[] = $point;
        }

        $this->vars['rows'] = str_replace('"', '', substr(substr(json_encode($points), 1), 0, -1));
    }

}
