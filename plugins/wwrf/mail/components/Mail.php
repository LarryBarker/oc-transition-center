<?php namespace Wwrf\Mail\Components;

use Cms\Classes\ComponentBase;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Auth;
use Carbon\Carbon;

class Mail extends ComponentBase
{
    /*
     * @var array
     * 
     */
    public $messages;

    public function componentDetails()
    {
        return [
            'name'        => 'Mail',
            'description' => 'Default mail component.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {

    }
    public function onRun()
    {
        /*$this->frontUser = Auth::getUser();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$tokenCache = new \Wwrf\Mail\App\TokenStore\TokenCache){
            return Redirect::to('/mail/signin');
        }

        $graph = new Graph();
        $graph->setAccessToken($tokenCache->getAccessToken());

        $user = $graph->createRequest('GET', '/me')
                        ->setReturnType(Model\User::class)
                        ->execute();

        $folderName = $this->frontUser->surname . ', ' . substr($this->frontUser->name, 0, 1);

        //$getFoldersUrl = '/me/mailfolders/inbox/childfolders?\$filter=contains(displayName,"'.$folderName.'")';

        $getFoldersUrl = "/me/mailfolders/inbox/childfolders?\$filter=contains(displayName,'$folderName')";

        $folder = $graph->createRequest('GET', $getFoldersUrl)
                        ->setReturnType(Model\Folder::class)
                        ->execute();

        $folderId = $folder[0]->getId();

        $messageQueryParams = array (
        // Only return Subject, ReceivedDateTime, and From fields
        //"\$select" => "subject,receivedDateTime,from",
        // Sort by ReceivedDateTime, newest first
        "\$orderby" => "receivedDateTime DESC",
        // Return at most 10 results
        //"\$top" => "10"
        );

        //$getMessagesUrl = '/me/mailfolders/inbox/messages?'.http_build_query($messageQueryParams);

        $getMessagesUrl = "/me/mailfolders/".$folderId."/messages?".http_build_query($messageQueryParams);
        
        $this->messages = $graph->createRequest('GET', $getMessagesUrl)
                        ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                        ->setReturnType(Model\Message::class)
                        ->execute();
        */
        $this->messages = \Wwrf\Mail\Controllers\OutlookController::getMail();
    }
}
