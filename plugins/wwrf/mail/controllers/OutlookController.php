<?php

namespace Wwrf\Mail\Controllers;

use Backend\Classes\Controller;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Redirect;
use Auth;

class OutlookController extends Controller
{
    public static function getMail() 
    {
        $frontUser = Auth::getUser();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$tokenCache = new \Wwrf\Mail\Classes\TokenCache){
            return Redirect::to('/mail/signin');
        }

        $graph = new Graph();
        $graph->setAccessToken($tokenCache->getAccessToken());

        $user = $graph->createRequest('GET', '/me')
                        ->setReturnType(Model\User::class)
                        ->execute();

        $folderName = $frontUser->surname.', '.substr($frontUser->name, 0, 1);

        $getFoldersUrl = "/me/mailfolders/inbox/childfolders?\$filter=contains(displayName,'$folderName')";

        $folder = $graph->createRequest('GET', $getFoldersUrl)
                        ->setReturnType(Model\Folder::class)
                        ->execute();

        $folderId = $folder[0]->getId();

        $_SESSION['folderId'] = $folderId;        

        $messageQueryParams = array (
        // Only return Subject, ReceivedDateTime, and From fields
        //"\$select" => "subject,receivedDateTime,from",
        // Sort by ReceivedDateTime, newest first
        "\$orderby" => "receivedDateTime DESC",
        // Return at most 10 results
        //"\$top" => "10"
        );

        $getMessagesUrl = "/me/mailfolders/".$folderId."/messages?".http_build_query($messageQueryParams);
        
        $messages = $graph->createRequest('GET', $getMessagesUrl)
                        ->addHeaders(array ('X-AnchorMailbox' => $user->getMail()))
                        ->setReturnType(Model\Message::class)
                        ->execute();
                    
        $_SESSION['message_count'] = count($messages);
                    
        return $messages;
    }

    public static function getMessage($id)
    {
        $id = $id;

        $getMessageUrl = "/me/messages/".$id;

        $token = $_SESSION['access_token'];

        $graph = new Graph();
        $graph->setAccessToken($_SESSION['access_token']);

        $message = $graph->createRequest('GET', $getMessageUrl)
                         ->setReturnType(Model\Message::class)
                         ->execute();

        return $message;
    }

    public static function getAfterFilters() {return [];}

    public static function getBeforeFilters() {return [];}

    public static function getMiddleware() {return [];}

    public function callAction($method, $parameters=false) {

        return call_user_func_array(array($this, $method), $parameters);

    }

}