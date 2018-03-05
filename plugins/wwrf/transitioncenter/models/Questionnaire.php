<?php namespace Wwrf\TransitionCenter\Models;

use Model;

/**
 * Model
 */
class Questionnaire extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_surveys_questions';

    protected $fillable = [
        'interest_profiler',
        'is_veteran',
        'can_type',
        'owd_class',
        'computer_skill',
        'workready_cert',
        'kansasworks',
        'owes_childsupport',
        'applied_online',
        'felony_type',
        'workforce',
        'has_resume',
        'three_speech',
        'continue_education',
        'has_pov',
        'user_id',
    ];
    public $belongsTo =[
        'user' => 'RainLab\User\Models\User',
        'order' => 'surname desc',
    ];

    public static function getFromUser($user)
    {
        if ($user->questionnaire)
            return $user->questionnaire;

        if (!$user->questionnaire && $user->groups[0]->name == 'Users')
        {
            $questionnaire = new static;
            $questionnaire->user = $user;
            $questionnaire->save();
        }

        $user->questionnaire = $questionnaire;

        return $questionnaire;
    }
}