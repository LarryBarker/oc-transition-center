<?php namespace Wwrf\Mail\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Microsoft\Graph\Model\Message;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class MailMessage extends ComponentBase
{
    /**
     * @var string
     */
    public $message;

    public $viewToken;

    public $messageId;
    
    public $attachments;
    
    public function componentDetails()
    {
        return [
            'name'        => 'mailMessage Component',
            'description' => 'Email message component...'
        ];
    }

    public function defineProperties()
    {
        return [            
            'id' => [
                'title'       => 'Message Id',
                'description' => 'Message Id parameter...',
                'default'     => '{{ :id }}',
                'type'        => 'string'
            ],];
    }

    public function onRun()
    {
        $this->message = $this->getMessage()['message'];
    }

    public function getMessage()
    {
        $id = $this->messageId = $this->property('id');
        
        //$this->message = new Message;

        $getMessageUrl = "/me/messages('".$id."')";

        $token = $_SESSION['access_token'];

        $graph = new Graph();
        $graph->setAccessToken($_SESSION['access_token']);

        $message = $graph->createRequest('GET', $getMessageUrl)
                            ->setReturnType(Model\Message::class)
                            ->execute();

        if($message->getHasAttachments()){

            $getAttachmentsUrl = "/me/messages('".$id."')/attachments";

            $attachments = $graph->createRequest('GET', $getAttachmentsUrl)
                    ->setReturnType(Model\Attachment::class)
                    ->execute();

            $this->attachments = $attachments;
        }

        return [
            'message' => $message
        ];
    }

    public function getAttachment($id)
    {
        $attachment = new Model\Attachment();
        $getAttachmentUrl = "/me/messages('".$this->messageId."')/attachments".$id;
        
        $attachment = $graph->createRequest('GET', $getAttachmentsUrl)
                            //->setReturnType(Model\Attachment::class)
                            ->execute();
        //$attachments = $this->message->getAttachments();

        return $attachment->getWebUrl();
    }
}
