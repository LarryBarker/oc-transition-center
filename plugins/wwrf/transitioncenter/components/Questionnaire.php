<?php namespace Wwrf\TransitionCenter\Components;



use Lang;

use Cms\Classes\ComponentBase;

use RainLab\Builder\Classes\ComponentHelper;;

use SystemException;

use Auth;

use Wwrf\TransitionCenter\Controllers\Questionnaires as QuestionnaireController;

use Wwrf\TransitionCenter\Models\Questionnaire as QuestionnaireModel;

use Flash;



class Questionnaire extends ComponentBase

{



    /**

     * A model instance to display

     * @var \October\Rain\Database\Model

     */

    public $record = null;



    // 

    // Details

    //

    public function componentDetails()

    {

        return [

            'name'        => 'Questionnaire Component',

            'description' => 'Adds the user survey components to the page...'

        ];

    }



    //

    // Properties

    //



    public function defineProperties()

    {

        return [

            'modelClass' => [

                'title'       => 'Model Class',

                'type'        => 'dropdown',

                'showExternalParam' => false

            ]

        ];

    }



    public function onRun()

    {

        //$this->addCss('/modules/system/assets/ui/storm.css');

        // Build a back-end form with the context of 'frontend'

        $formController = new \Wwrf\TransitionCenter\Controllers\Questionnaires();

        //$formController = new QuestionnaireController();

        $formController->create('frontend');

        //Append the formController to the page

        $this->page['form'] = $formController;

    }



    public function onSave()

    {

        $user = Auth::getUser();



        if (!$user->questionnaire)

            QuestionnaireModel::create(['user_id' => $user->id]);

        

        return [

            'error' => QuestionnaireModel::where('user_id', $user->id)

                       ->update(post('Questionnaire')),
            'flash' => Flash::success('Your survey has been submitted. Thank you!')

        ];

    }



    public function getModelClassOptions()

    {

        return ComponentHelper::instance()->listGlobalModels();

    }

}