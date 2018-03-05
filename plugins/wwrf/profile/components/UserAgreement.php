<?php namespace Wwrf\Profile\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Theme;
use SystemException;
use File;
use Lang;
use RainLab\User\Models\User as UserModel;
use October\Rain\Database\Model;
use Auth;

class UserAgreement extends ComponentBase
{
    use \System\Traits\ViewMaker;
    
    public function componentDetails()
    {
        return [
            'name'        => 'User Agreement Component',
            'description' => 'Render user agreement in modal dialog.'
        ];
    }
    public function defineProperties()
    {
        return [
            'title' => [
                'title' => 'Modal Title',
            ],
            'partial' => [
                'title'         => 'Partial',
                'description'   => 'Name of the partial in your theme directory',
            ],
        ];
    }
    public function onRun()
    {
        
    }
    public function onUserAgreement()
    {
        $user = Auth::getUser();    
        
        $user->update(['user_agreement' => date('Y-m-d H:i:s')]);
  
        $user->save();

        return $user;
    }
}